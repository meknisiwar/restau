<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class ReservationAdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_reservations')]
    public function index(ReservationRepository $reservationRepository, Request $request): Response
    {
        $filter = $request->query->get('filter', 'all');
        
        if ($filter === 'upcoming') {
            $reservations = $reservationRepository->findUpcomingReservations();
        } else {
            $reservations = $reservationRepository->findRecentReservations();
        }

        return $this->render('admin/reservations/index.html.twig', [
            'reservations' => $reservations,
            'filter' => $filter,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservations/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/status', name: 'app_admin_reservation_status', methods: ['POST'])]
    public function updateStatus(Reservation $reservation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newStatus = $request->request->get('status');
        
        if (in_array($newStatus, ['pending', 'confirmed', 'cancelled', 'completed'])) {
            $reservation->setStatus($newStatus);
            $entityManager->flush();
            
            $this->addFlash('success', 'Reservation status updated to: ' . $newStatus);
        } else {
            $this->addFlash('error', 'Invalid status');
        }

        return $this->redirectToRoute('app_admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}/delete', name: 'app_admin_reservation_delete', methods: ['POST'])]
    public function delete(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Reservation deleted successfully!');
        return $this->redirectToRoute('app_admin_reservations');
    }
}

