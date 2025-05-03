<?php

namespace App\Controller;

use LogicException;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, CategoriesRepository $categoriesRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // conditionnel pour vérifier si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            // Si l'utilisateur est déjà connecté, on le redirige vers la page du compte de l'utilisateur
            return $this->redirectToRoute('app_profils_index');
        }
        $categories = $categoriesRepository->findAll(); // On récupère toutes les catégories

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'categories' => $categories, // On passe les catégories récupérées à la vue 
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
