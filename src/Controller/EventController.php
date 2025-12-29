<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/events')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index')]
    public function index(EventRepository $repo): Response
    {
        $events = $repo->findUpcoming();
        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/{id}', name: 'app_event_show', requirements: ['id' => '\\d+'])]
    public function show(Event $event, \App\Repository\RsvpRepository $rsvpRepo): Response
    {
        $rsvpCount = $rsvpRepo->countForEvent($event->getId());
        $userRsvp = null;
        if ($this->getUser()) {
            $userRsvp = $rsvpRepo->findByUserAndEvent($this->getUser()->getId(), $event->getId());
        }

        return $this->render('event/show.html.twig', ['event' => $event, 'rsvpCount' => $rsvpCount, 'userRsvp' => $userRsvp]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request;
            $event = new Event();
            $event->setTitle($data->get('title'));
            $event->setDescription($data->get('description'));
            $event->setStartAt(new \DateTimeImmutable($data->get('startAt')));
            $event->setEndAt($data->get('endAt') ? new \DateTimeImmutable($data->get('endAt')) : null);
            $event->setLocation($data->get('location'));
            $event->setImage($data->get('image'));
            $event->setPublished((bool)$data->get('published'));

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Event created');
            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/new.html.twig');
    }    #[Route('/{id}/rsvp', name: 'app_event_rsvp', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function rsvp(Event $event, Request $request, EntityManagerInterface $em, \App\Repository\RsvpRepository $rsvpRepo): Response
    {
        // Check CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('event_rsvp_' . $event->getId(), $token)) {
            $this->addFlash('error', 'Invalid security token.');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        $user = $this->getUser();
        $guests = max(1, (int)$request->request->get('guests', 1));

        $existing = $rsvpRepo->findByUserAndEvent($user->getId(), $event->getId());
        if ($existing) {
            $this->addFlash('info', 'You have already registered for this event.');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        $rsvp = new \App\Entity\Rsvp();
        $rsvp->setUser($user);
        $rsvp->setEvent($event);
        $rsvp->setGuests($guests);

        $em->persist($rsvp);
        $em->flush();

        $this->addFlash('success', 'Registration confirmed.');
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/{id}/cancel', name: 'app_event_rsvp_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelRsvp(Event $event, Request $request, EntityManagerInterface $em, \App\Repository\RsvpRepository $rsvpRepo): Response
    {
        // Check CSRF token
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('cancel_rsvp_' . $event->getId(), $token)) {
            $this->addFlash('error', 'Invalid security token.');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        $user = $this->getUser();
        $existing = $rsvpRepo->findByUserAndEvent($user->getId(), $event->getId());
        if ($existing) {
            $em->remove($existing);
            $em->flush();
            $this->addFlash('success', 'Your registration has been cancelled.');
        } else {
            $this->addFlash('info', 'No registration found.');
        }
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/my-registrations', name: 'app_user_events')]
    #[IsGranted('ROLE_USER')]
    public function myRegistrations(\App\Repository\RsvpRepository $rsvpRepo): Response
    {
        $user = $this->getUser();
        $rsvps = $rsvpRepo->findBy(['user' => $user], ['createdAt' => 'DESC']);
        
        return $this->render('event/my_registrations.html.twig', [
            'rsvps' => $rsvps,
        ]);
    }
}
