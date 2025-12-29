<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/reviews')]
class ReviewController extends AbstractController
{    #[Route('/product/{id}', name: 'app_review_product', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function submit(Request $request, Product $product, EntityManagerInterface $em, ReviewRepository $reviews, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Validate CSRF token
        $token = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('review_' . $product->getId(), $token))) {
            $this->addFlash('danger', 'Invalid security token. Please try again.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        $rating = (int)$request->request->get('rating', 5);
        $comment = trim($request->request->get('comment', ''));

        if ($rating < 1 || $rating > 5 || strlen($comment) < 2) {
            $this->addFlash('danger', 'Please provide a valid rating (1-5) and a comment with at least 2 characters.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Check if user already reviewed this product
        $existingReview = $reviews->findOneBy(['user' => $this->getUser(), 'product' => $product]);
        if ($existingReview) {
            $this->addFlash('warning', 'You have already reviewed this product.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        try {
            $review = new Review();
            $review->setProduct($product);
            $review->setUser($this->getUser());
            $review->setRating($rating);
            $review->setComment($comment);
            $review->setActive(true);

            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Thank you for your review!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error saving your review. Please try again.');
        }

        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }
}
