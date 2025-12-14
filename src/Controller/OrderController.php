<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\CouponRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
        private CartRepository $cartRepository,
        private CouponRepository $couponRepository
    ) {
    }

    #[Route('/', name: 'app_order_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $orders = $this->orderRepository->findByUser($user);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/apply-coupon', name: 'app_order_apply_coupon', methods: ['POST'])]
    public function applyCoupon(Request $request): JsonResponse
    {
        $code = $request->request->get('code');
        if (!$code) {
            return new JsonResponse(['ok' => false, 'message' => 'Code required'], 400);
        }
        $coupon = $this->couponRepository->findActiveByCode($code);
        if (!$coupon) {
            return new JsonResponse(['ok' => false, 'message' => 'Invalid or expired code'], 404);
        }
        return new JsonResponse(['ok' => true, 'type' => $coupon->getType(), 'amount' => (float)$coupon->getAmount()]);
    }

    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(Request $request): Response
    {
        $user = $this->getUser();
        $cart = $this->cartRepository->findActiveCartByUser($user);

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty!');
            return $this->redirectToRoute('app_cart_index');
        }

        if ($request->isMethod('POST')) {
            $order = new Order();
            $order->setUser($user);
            $order->setStatus(Order::STATUS_PENDING);
            $order->setDeliveryAddress($request->request->get('delivery_address') ?? $user->getAddress());
            $order->setPhone($request->request->get('phone') ?? $user->getPhone());
            $order->setNotes($request->request->get('notes'));

            // coupon handling
            $couponCode = $request->request->get('coupon_code');
            $discountAmount = 0.0;
            if ($couponCode) {
                $coupon = $this->couponRepository->findActiveByCode($couponCode);
                if ($coupon) {
                    // compute discount after items added
                } else {
                    $this->addFlash('error', 'Coupon invalid or expired');
                }
            }

            // Create order items from cart items
            foreach ($cart->getCartItems() as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->setOrderRef($order);
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setPrice($cartItem->getPrice());
                $orderItem->setProductName($cartItem->getProduct()->getName());

                $this->entityManager->persist($orderItem);
            }

            $order->calculateTotal();

            // apply coupon if present
            if (!empty($coupon) && $coupon->isActive()) {
                $total = (float)$order->getTotalAmount();
                if ($coupon->getType() === 'percent') {
                    $discountAmount = $total * ((float)$coupon->getAmount() / 100.0);
                } else {
                    $discountAmount = (float)$coupon->getAmount();
                }
                $newTotal = max(0, $total - $discountAmount);
                $order->setTotalAmount(number_format($newTotal, 2, '.', ''));

                $coupon->incrementUsedCount();
                $this->entityManager->persist($coupon);
            }

            $this->entityManager->persist($order);

            // Clear cart
            foreach ($cart->getCartItems() as $item) {
                $this->entityManager->remove($item);
            }
            $cart->setActive(false);

            // loyalty points: give 1 point per 10 DT spent
            $points = floor((float)$order->getTotalAmount() / 10);
            if (method_exists($user, 'setLoyaltyPoints')) {
                $existing = $user->loyaltyPoints ?? 0;
                $user->loyaltyPoints = $existing + $points;
                $this->entityManager->persist($user);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Order placed successfully!');
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        return $this->render('order/checkout.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', requirements: ['id' => '\d+'])]
    public function show(Order $order): Response
    {
        // Ensure user can only view their own orders
        if ($order->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}

