<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Rsvp;
use App\Repository\EventRepository;
use App\Repository\RsvpRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/events')]
#[IsGranted('ROLE_ADMIN')]
class EventAdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy([], ['startAt' => 'DESC']);
        
        return $this->render('admin/event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_event_show')]
    public function show(Event $event, RsvpRepository $rsvpRepository): Response
    {
        $rsvps = $rsvpRepository->findBy(['event' => $event], ['createdAt' => 'DESC']);
        $rsvpCount = $rsvpRepository->countForEvent($event->getId());
        
        return $this->render('admin/event/show.html.twig', [
            'event' => $event,
            'rsvps' => $rsvps,
            'rsvpCount' => $rsvpCount,
        ]);
    }    #[Route('/{id}/edit', name: 'app_admin_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            // Check CSRF token
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('edit_event_' . $event->getId(), $token)) {
                $this->addFlash('error', 'Invalid security token.');
                return $this->redirectToRoute('app_admin_event_edit', ['id' => $event->getId()]);
            }

            $data = $request->request;
            
            $event->setTitle($data->get('title'));
            $event->setDescription($data->get('description'));
            $event->setStartAt(new \DateTimeImmutable($data->get('startAt')));
            $event->setEndAt($data->get('endAt') ? new \DateTimeImmutable($data->get('endAt')) : null);
            $event->setLocation($data->get('location'));
            $event->setImage($data->get('image'));
            $event->setPublished((bool)$data->get('published'));

            $em->flush();
            $this->addFlash('success', 'Event updated successfully.');
            
            return $this->redirectToRoute('app_admin_event_show', ['id' => $event->getId()]);
        }

        return $this->render('admin/event/edit.html.twig', [
            'event' => $event,
        ]);
    }#[Route('/{id}/toggle', name: 'app_admin_event_toggle', methods: ['POST'])]
    public function toggle(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        // Check CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('toggle_event_' . $event->getId(), $token)) {
            $this->addFlash('error', 'Invalid security token.');
            return $this->redirectToRoute('app_admin_events');
        }

        $event->setPublished(!$event->isPublished());
        $em->flush();
        
        $status = $event->isPublished() ? 'published' : 'unpublished';
        $this->addFlash('success', "Event {$status} successfully.");
        
        return $this->redirectToRoute('app_admin_events');
    }

    #[Route('/{id}/delete', name: 'app_admin_event_delete', methods: ['POST'])]
    public function delete(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        // Check CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_event_' . $event->getId(), $token)) {
            $this->addFlash('error', 'Invalid security token.');
            return $this->redirectToRoute('app_admin_events');
        }

        $title = $event->getTitle();
        $em->remove($event);
        $em->flush();
        
        $this->addFlash('success', "Event '{$title}' deleted successfully.");
        
        return $this->redirectToRoute('app_admin_events');
    }

    #[Route('/{eventId}/rsvp/{rsvpId}/delete', name: 'app_admin_rsvp_delete', methods: ['POST'])]
    public function deleteRsvp(int $eventId, Rsvp $rsvp, Request $request, EntityManagerInterface $em): Response
    {
        // Check CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_rsvp_' . $rsvp->getId(), $token)) {
            $this->addFlash('error', 'Invalid security token.');
            return $this->redirectToRoute('app_admin_event_show', ['id' => $eventId]);
        }

        $userName = $rsvp->getUser()->getEmail();
        $em->remove($rsvp);
        $em->flush();
        
        $this->addFlash('success', "Registration for {$userName} cancelled.");
        
        return $this->redirectToRoute('app_admin_event_show', ['id' => $eventId]);
    }
}
