<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ProductCategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/products')]
#[IsGranted('ROLE_ADMIN')]
class ProductAdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/products/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_admin_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductCategoryRepository $categoryRepository, FileUploader $fileUploader): Response
    {
        if ($request->isMethod('POST')) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice((float) $request->request->get('price'));
            $product->setAvailable((bool) $request->request->get('available'));

            // Handle file upload
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $product->setImage('/uploads/products/' . $imageFileName);
            } elseif ($request->request->get('image_url')) {
                // Fallback to URL if provided
                $product->setImage($request->request->get('image_url'));
            }

            $categoryId = $request->request->get('category');
            if ($categoryId) {
                $category = $categoryRepository->find($categoryId);
                if ($category) {
                    $product->setCategory($category);
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully!');
            return $this->redirectToRoute('app_admin_products');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('admin/products/new.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager, ProductCategoryRepository $categoryRepository, FileUploader $fileUploader): Response
    {
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice((float) $request->request->get('price'));
            $product->setAvailable((bool) $request->request->get('available'));

            // Handle file upload
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                // Delete old image if it's a local file
                $oldImage = $product->getImage();
                if ($oldImage && str_starts_with($oldImage, '/uploads/products/')) {
                    $oldFileName = basename($oldImage);
                    $fileUploader->delete($oldFileName);
                }

                $imageFileName = $fileUploader->upload($imageFile);
                $product->setImage('/uploads/products/' . $imageFileName);
            } elseif ($request->request->get('image_url')) {
                // Update to URL if provided
                $product->setImage($request->request->get('image_url'));
            }

            $categoryId = $request->request->get('category');
            if ($categoryId) {
                $category = $categoryRepository->find($categoryId);
                if ($category) {
                    $product->setCategory($category);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('app_admin_products');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('admin/products/edit.html.twig', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Product deleted successfully!');
        return $this->redirectToRoute('app_admin_products');
    }

    #[Route('/{id}/toggle', name: 'app_admin_product_toggle', methods: ['POST'])]
    public function toggle(Product $product, EntityManagerInterface $entityManager): Response
    {
        $product->setAvailable(!$product->isAvailable());
        $entityManager->flush();

        $this->addFlash('success', 'Product availability updated!');
        return $this->redirectToRoute('app_admin_products');
    }
}

