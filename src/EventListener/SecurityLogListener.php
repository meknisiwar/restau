<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Psr\Log\LoggerInterface;

class SecurityLogListener implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'security.interactive_login' => 'onSecurityInteractiveLogin',
            'security.authentication.failure' => 'onAuthenticationFailure',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();
        
        $this->logger->info('User login successful', [
            'username' => $user->getUserIdentifier(),
            'ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent')
        ]);
    }

    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $request = $event->getRequest();
        
        $this->logger->warning('Authentication failure', [
            'ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent'),
            'attempted_username' => $request->request->get('email')
        ]);
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        
        // Log les tentatives d'accès non autorisées
        if ($exception instanceof \Symfony\Component\Security\Core\Exception\AccessDeniedException) {
            $this->logger->warning('Access denied', [
                'ip' => $request->getClientIp(),
                'uri' => $request->getUri(),
                'user_agent' => $request->headers->get('User-Agent')
            ]);
        }
    }
}
