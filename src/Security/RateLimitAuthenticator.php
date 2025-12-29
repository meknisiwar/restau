<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AbstractPreAuthenticatedAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

class RateLimitAuthenticator extends AbstractPreAuthenticatedAuthenticator implements 
    AuthenticationFailureHandlerInterface, 
    AuthenticationSuccessHandlerInterface
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900; // 15 minutes

    public function __construct(
        private CacheInterface $cache
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function getPreAuthenticatedData(Request $request): array
    {
        $ip = $request->getClientIp();
        $key = 'login_attempts_' . $ip;
        
        $attempts = $this->cache->get($key, function() {
            return 0;
        });

        if ($attempts >= self::MAX_ATTEMPTS) {
            throw new AuthenticationException('Too many login attempts. Please try again later.');
        }

        return [$ip, null];
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $ip = $request->getClientIp();
        $key = 'login_attempts_' . $ip;
        
        $attempts = $this->cache->get($key, function() {
            return 0;
        });
        
        $attempts++;
        $this->cache->delete($key);
        
        $item = $this->cache->getItem($key);
        $item->set($attempts);
        $item->expiresAfter(self::LOCKOUT_TIME);
        $this->cache->save($item);

        return new Response('Authentication failed', Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        $ip = $request->getClientIp();
        $key = 'login_attempts_' . $ip;
        $this->cache->delete($key);

        return new Response('Success', Response::HTTP_OK);
    }
}
