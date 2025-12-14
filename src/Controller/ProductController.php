<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index')]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository
    ): Response {
        $categoryId = $request->query->get('category');
        $q = $request->query->get('q');

        // If there is a search query, use the searchAvailable method which optionally
        // filters by category as well. Otherwise fall back to previous behavior.
        if ($q !== null && trim($q) !== '') {
            $products = $productRepository->searchAvailable($q, $categoryId ? (int)$categoryId : null);
        } else {
            if ($categoryId) {
                $category = $categoryRepository->find($categoryId);
                $products = $category ? $productRepository->findByCategory($category->getId()) : [];
            } else {
                $products = $productRepository->findAvailableProducts();
            }
        }

        $categories = $categoryRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categoryId ? (int)$categoryId : null,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(Product $product, ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findByProduct($product->getId());

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }
}

