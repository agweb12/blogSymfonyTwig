<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account/articles')]
final class ArticlesController extends AbstractController
{
    #[Route(name: 'app_my_articles', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository, CategoriesRepository $categoriesRepository): Response
    {
        $user = $this->getUser(); // On récupère l'utilisateur connecté
        $categories = $categoriesRepository->findAll(); // On récupère toutes les catégories
        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findByUser($user), // On utilise la méthode findByUser pour récupérer les articles de l'utilisateur
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/new', name: 'app_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // On récupère l'utilisateur connecté
            $article->setUser($user); // On associe l'article à l'utilisateur

            // Gestion de l'image
            $image = $form->get('image')->getData(); // On récupère l'image dont entre autres la valeur qu'on lui a insérée

            // Le nom d'origine de l'image
            $originalImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            // Sécurisation du nom du fichier
            $safeImage = $slugger->slug($originalImage); // On utilise le slugger pour sécuriser le nom du fichier. Que fait il ? Le slugger remplace les caractères spéciaux par des tirets et met le nom en minuscules

            // On génère un nom unique pour l'image en ajoutant un identifiant unique
            $newImage = $safeImage.'-'.uniqid().'.'.$image->guessExtension(); // uniqid() génère un identifiant unique et guessExtension() permet de détecter l'extension du fichier

            // On déplace le fichier dans le répertoire de destination
            $image->move(
                $this->getParameter('article_pictures_directory'), // On récupère le répertoire de destination
                $newImage // On déplace le fichier dans le répertoire de destination
            );

            $article->setImage($newImage); // On associe le nom de l'image à l'entité Profils : mise à jour de la propriété picture dans $profil (object) avec le nouveau nom de l'image

            // Récupération de la catégorie sélectionné
            $categories = $article->getCategory()->getValues();
            foreach ($categories as $category) {
                $category->addArticle($article); // On associe l'article à la catégorie
                $entityManager->persist($category); // On persiste la catégorie
                // on remplie la table de jointure
                // $article->addCategory($category); // On associe la catégorie à l'article
                // $entityManager->persist($article); // On persiste l'article
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_articles', [], Response::HTTP_SEE_OTHER);
        }

        $categories = $categoriesRepository->findAll();
        return $this->render('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/{id}', name: 'app_articles_show', methods: ['GET'])]
    public function show(Articles $article, CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll();
        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser(); // On récupère l'utilisateur connecté
            $article->setUser($user); // On associe l'article à l'utilisateur

            // Gestion de l'image
            $image = $form->get('image')->getData(); // On récupère l'image dont entre autres la valeur qu'on lui a insérée

            // Le nom d'origine de l'image
            $originalImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            // Sécurisation du nom du fichier
            $safeImage = $slugger->slug($originalImage); // On utilise le slugger pour sécuriser le nom du fichier. Que fait il ? Le slugger remplace les caractères spéciaux par des tirets et met le nom en minuscules

            // On génère un nom unique pour l'image en ajoutant un identifiant unique
            $newImage = $safeImage.'-'.uniqid().'.'.$image->guessExtension(); // uniqid() génère un identifiant unique et guessExtension() permet de détecter l'extension du fichier

            try{
                // On déplace le fichier dans le répertoire de destination
                $image->move(
                    $this->getParameter('article_pictures_directory'), // On récupère le répertoire de destination
                    $newImage // On déplace le fichier dans le répertoire de destination
                );
            } catch (FileException $th) {
                // Si une erreur se produit lors du déplacement du fichier, on peut gérer l'erreur ici
                // Par exemple, on peut afficher un message d'erreur ou enregistrer l'erreur dans un journal
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                return $this->redirectToRoute('app_articles_new');
            }

            $article->setImage($newImage); // On associe le nom de l'image à l'entité Profils : mise à jour de la propriété picture dans $profil (object) avec le nouveau nom de l'image

            // Récupération de la catégorie sélectionné
            $categories = $article->getCategory()->getValues();
            foreach ($categories as $category) {
                $category->addArticle($article); // On associe l'article à la catégorie
                $entityManager->persist($category); // On persiste la catégorie
                // on remplie la table de jointure
                // $article->addCategory($category); // On associe la catégorie à l'article
                // $entityManager->persist($article); // On persiste l'article
            }
            $entityManager->flush();

            $idArticle = $article->getId(); // On récupère l'id de l'article
            // redirection vers la page de l'article
            return $this->redirectToRoute('app_articles_show', ['id' => $idArticle], Response::HTTP_SEE_OTHER);
        }
        $categories = $categoriesRepository->findAll();
        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/{id}', name: 'app_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_my_articles', [], Response::HTTP_SEE_OTHER);
    }
}
