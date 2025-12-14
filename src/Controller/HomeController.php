<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository
    ): Response {
        $products = $productRepository->findAvailableProducts();
        $categories = $categoryRepository->findAll();

        return $this->render('home/pro_index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}

