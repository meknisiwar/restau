<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        ReservationRepository $reservationRepository
    ): Response {
        // Get all orders and calculate revenue
        $allOrders = $orderRepository->findAll();
        $totalRevenue = 0;
        $todayRevenue = 0;
        $today = new \DateTime('today');
        
        foreach ($allOrders as $order) {
            if ($order->getStatus() !== 'cancelled') {
                $totalRevenue += $order->getTotalAmount();
                if ($order->getCreatedAt() >= $today) {
                    $todayRevenue += $order->getTotalAmount();
                }
            }
        }
        
        $stats = [
            'total_users' => count($userRepository->findAll()),
            'total_products' => count($productRepository->findAll()),
            'total_orders' => count($allOrders),
            'pending_orders' => count($orderRepository->findByStatus('pending')),
            'total_reservations' => count($reservationRepository->findAll()),
            'upcoming_reservations' => count($reservationRepository->findUpcomingReservations()),
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
        ];
        
        // Get recent orders (last 5)
        $recentOrders = $orderRepository->findRecentOrders(5);
        
        // Get upcoming reservations (next 5)
        $upcomingReservations = $reservationRepository->findUpcomingReservations(5);
        
        // Get recent users (last 5)
        $recentUsers = $userRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'upcomingReservations' => $upcomingReservations,
            'recentUsers' => $recentUsers,
        ]);
    }
}

