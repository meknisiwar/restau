<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{
    #[Route('/change-language/{locale}', name: 'app_change_language')]
    public function changeLanguage(string $locale, Request $request): Response
    {
        // Validate locale
        $supportedLocales = ['en', 'fr', 'ar'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }

        // Store locale in session
        $request->getSession()->set('_locale', $locale);

        // Redirect back to the previous page or homepage
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_home');
    }
}
