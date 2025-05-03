<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route; // elle utilise l'annotation Route pour les routes
use Symfony\Component\HttpFoundation\Response; // elle utilise la classe Response pour les réponses HTTP

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticlesRepository $articlesRepository, CategoriesRepository $categoriesRepository): Response{
        $limit = 6; // on définit le nombre d'articles à afficher
        // Tu vas intéroger la base de données pour récupérer les articles
        $articles = $articlesRepository->findRecentsArticles($limit); // on utilise le repository pour récupérer le nombre d'articles que l'on souhaite
        $categories = $categoriesRepository->findAll(); // on récupère toutes les catégories
        return $this->render('home/home.html.twig', [
            'articleRecents' => $articles, // on passe les articles récupérés à la vue
            'title' => 'Nos Actualités', // on passe le titre à la vue
            'limit' => $limit, // on passe la limite à la vue
            'description' => 'Affichage des articles récents', // on passe la description à la vue
            'categories' => $categories, // on passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/allArticles', name: 'app_home_allArticles')]
    public function allArticles(ArticlesRepository $articlesRepository, CategoriesRepository $categoriesRepository): Response{
        // Tu vas intéroger la base de données pour récupérer les articles
        $articles = $articlesRepository->findAll(); // On récupère tous les articles
        $categories = $categoriesRepository->findAll(); // on récupère toutes les catégories
        return $this->render('home/home.html.twig', [
            'articles' => $articles, // on passe les articles récupérés à la vue
            'title' => 'Tous les articles', // on passe le titre à la vue
            'description' => 'Affichage de tous les articles', // on passe la description à la vue
            'categories' => $categories, // on passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/article/{id}', name: 'app_one_article')] 
    public function showArticle(ArticlesRepository $articlesRepository, $id, CategoriesRepository $categoriesRepository): Response{
        // ON va intérroger la base de données pour récupérer l'affichage d'une article
        $oneArticle = $articlesRepository->findOneBy(['id' => $id]); // On récupère l'article en fonction de son id
        $title = $oneArticle->getTitle(); // on récupère le titre de l'article
        $categories = $categoriesRepository->findAll(); // on récupère toutes les catégories

        return $this->render('home/home.html.twig', [
            'oneArticle' => $oneArticle, // on passe l'article récupéré à la vue
            'title' => 'Article : ' . $title, // on passe le titre à la vue
            'description' => 'Affichage de l\'article', // on passe la desc ription à la vue
            'categories' => $categories, // on passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_articles')]
    public function showCategory(ArticlesRepository $articlesRepository, $id, CategoriesRepository $categoriesRepository): Response {
        // On va intérroger la base de données pour récupérer l'affichage d'une catégorie
        $articles = $articlesRepository->findByCategory($id); // On récupère les articles associés à la catégorie
        // dd($articles); // On affiche les articles pour le débuggage
        if (!$articles) {
            throw $this->createNotFoundException('Aucun article trouvé pour cette catégorie'); // Si aucun article n'est trouvé, on lance une exception
        }
        $categories = $categoriesRepository->findAll(); // on récupère toutes les catégories pour la barre de navigation
        $category = $categoriesRepository->find($id); // on récupère la catégorie en fonction de son id
        if (!$category) {
            throw $this->createNotFoundException('Aucune catégorie trouvée'); // Si aucune catégorie n'est trouvée, on lance une exception
        }
        $title = $category->getName(); // on récupère le nom de la catégorie
        return $this->render('home/home.html.twig', [
            'articles' => $articles, // on passe les articles récupérés à la vue
            'categories' => $categories, // on passe les catégories récupérées à la vue
            'title' => 'Articles de la catégorie : ' . $title, // on passe le titre à la vue
            'description' => 'Affichage des articles de la catégorie : ' . $title, // on passe la description à la vue
            'category' => $id, // on passe la catégorie récupérée à la vue
        ]);
    }
}
