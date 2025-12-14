<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/orders')]
#[IsGranted('ROLE_ADMIN')]
class OrderAdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_orders')]
    public function index(OrderRepository $orderRepository, Request $request): Response
    {
        $status = $request->query->get('status');
        
        if ($status) {
            $orders = $orderRepository->findByStatus($status);
        } else {
            $orders = $orderRepository->findRecentOrders();
        }

        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
            'selectedStatus' => $status,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_order_show')]
    public function show(Order $order): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/status', name: 'app_admin_order_status', methods: ['POST'])]
    public function updateStatus(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newStatus = $request->request->get('status');
        
        if (in_array($newStatus, ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])) {
            $order->setStatus($newStatus);
            $entityManager->flush();
            
            $this->addFlash('success', 'Order status updated to: ' . $newStatus);
        } else {
            $this->addFlash('error', 'Invalid status');
        }

        return $this->redirectToRoute('app_admin_order_show', ['id' => $order->getId()]);
    }

    #[Route('/{id}/delete', name: 'app_admin_order_delete', methods: ['POST'])]
    public function delete(Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();

        $this->addFlash('success', 'Order deleted successfully!');
        return $this->redirectToRoute('app_admin_orders');
    }
}

