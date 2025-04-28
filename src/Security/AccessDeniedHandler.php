<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface 
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException) : RedirectResponse// méthode est exécuté dès que symfony détercte une tentaie d'acces itnerdit
    {
        // Handle the access denied exception
        // You can redirect to a specific route or return a custom response
        return new RedirectResponse('/'); // Redirection vers la page d'accueil
    }
}