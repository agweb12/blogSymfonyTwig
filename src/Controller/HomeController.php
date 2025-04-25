<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response; // elle utilise la classe Response pour les réponses HTTP
use Symfony\Component\Routing\Annotation\Route; // elle utilise l'annotation Route pour les routes

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')] // C'est une annotation qui définit la route pour cette action : /home/{name} est le chemin d'URL et app_home_name est le nom de la route : il nous sert à générer des URL dans l'application dans la barre de navigation

    // dans le cas où je souhaite créer le chemin à la racine, je peux faire : #[Route('/', name: 'app_home')]
    public function index(): Response // C'est une action qui renvoie une réponse HTTP
    {
        return $this->render('home/home.html.twig', [ //elle retoure la vue de la méthode render qui génère une réponse HTTP en utilisant un template Twig
            'controller_name' => 'HomeController', // c'est une variable qui est passée au template Twig : la clé est une variable que l'on utilise dans la vue et la valeur c'est la valeur de la variable
        ]);
    }
}
