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

#[Route('/reviews')]
class ReviewController extends AbstractController
{
    #[Route('/product/{id}', name: 'app_review_product', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function submit(Request $request, Product $product, EntityManagerInterface $em, ReviewRepository $reviews): Response
    {
        $rating = (int)$request->request->get('rating', 5);
        $comment = trim($request->request->get('comment', ''));

        if ($rating < 1 || $rating > 5 || strlen($comment) < 2) {
            $this->addFlash('danger', 'Please provide a valid rating (1-5) and a short comment.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        $review = new Review();
        $review->setProduct($product);
        $review->setUser($this->getUser());
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setActive(true);

        $em->persist($review);
        $em->flush();

        $this->addFlash('success', 'Thank you for your review!');
        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }
}
