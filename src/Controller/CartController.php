<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository
    ) {
    }

    #[Route('/', name: 'app_cart_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $cart = $this->cartRepository->findActiveCartByUser($user);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request): Response
    {
        $user = $this->getUser();
        $cart = $this->cartRepository->findActiveCartByUser($user);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setActive(true);
            $this->entityManager->persist($cart);
        }

        // Check if product already in cart
        $cartItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $cartItem = $item;
                break;
            }
        }

        $quantity = (int) $request->request->get('quantity', 1);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Product added to cart!');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(CartItem $cartItem, Request $request): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);

        if ($quantity > 0) {
            $cartItem->setQuantity($quantity);
            $this->entityManager->flush();
            $this->addFlash('success', 'Cart updated!');
        }

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(CartItem $cartItem): Response
    {
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();

        $this->addFlash('success', 'Item removed from cart!');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(): Response
    {
        $user = $this->getUser();
        $cart = $this->cartRepository->findActiveCartByUser($user);

        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                $this->entityManager->remove($item);
            }
            $this->entityManager->flush();
            $this->addFlash('success', 'Cart cleared!');
        }

        return $this->redirectToRoute('app_cart_index');
    }
}

