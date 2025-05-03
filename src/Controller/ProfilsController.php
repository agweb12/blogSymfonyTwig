<?php

namespace App\Controller;

use App\Entity\Profils;
use App\Form\ProfilsType;
use App\Repository\ProfilsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account')]
final class ProfilsController extends AbstractController
{
    #[Route(name: 'app_profils_index', methods: ['GET'])]
    public function index(ProfilsRepository $profilsRepository, CategoriesRepository $categoriesRepository): Response
    {
        $user = $this->getUser(); // On récupère l'utilisateur connecté
        $categories = $categoriesRepository->findAll(); // On récupère toutes les catégories
        return $this->render('account/account.html.twig', [
            'profil' => $profilsRepository->findByUser($user), // On utilise la méthode findByUser pour récupérer le profil de l'utilisateur
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    // Cela crée un nouveau profil
    // On utilise le formulaire ProfilsType pour créer un nouveau profil
    #[Route('/new', name: 'app_profils_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response // Je vais utiliser dans l'action new des services, des interfaces, des classes qui sont déjà présentes dans Symfony
    {
        $profil = new Profils();
        $form = $this->createForm(ProfilsType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // On récupère l'utilisateur connecté
            $profil->setUser($user); // On associe le profil à l'utilisateur

            // Gestion de l'image
            $picture = $form->get('picture')->getData(); // On récupère l'image dont entre autres la valeur qu'on lui a insérée

            // Le nom d'origine de l'image
            $originalPicture = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);

            // Sécurisation du nom du fichier
            $safePicture = $slugger->slug($originalPicture); // On utilise le slugger pour sécuriser le nom du fichier. Que fait il ? Le slugger remplace les caractères spéciaux par des tirets et met le nom en minuscules

            // On génère un nom unique pour l'image en ajoutant un identifiant unique
            $newPicture = $safePicture.'-'.uniqid().'.'.$picture->guessExtension(); // uniqid() génère un identifiant unique et guessExtension() permet de détecter l'extension du fichier

            // On déplace le fichier dans le répertoire de destination
            $picture->move(
                $this->getParameter('profile_pictures_directory'), // On récupère le répertoire de destination
                $newPicture // On déplace le fichier dans le répertoire de destination
            );

            $profil->setPicture($newPicture); // On associe le nom de l'image à l'entité Profils : mise à jour de la propriété picture dans $profil (object) avec le nouveau nom de l'image

            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
        }

        $categories = $categoriesRepository->findAll(); // On récupère toutes les catégories
        return $this->render('profils/new.html.twig', [
            'profil' => $profil,
            'form' => $form,
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/{id}', name: 'app_profils_show', methods: ['GET'])]
    public function show(Profils $profil, CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll(); // On récupère toutes les catégories
        return $this->render('profils/show.html.twig', [
            'profil' => $profil,
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profils_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profils $profil, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response
    {
        // On vérifie si l'utilisateur connecté est le même que celui du profil
        // Si ce n'est pas le cas, on redirige vers la page de l'utilisateur connecté
        if ($profil->getId() !== $this->getUser()->getProfil()->getId()) {
            return $this->redirectToRoute('app_profil_edit', ['id' => $this->getUser()->getProfil()->getId()], Response::HTTP_SEE_OTHER);
            
        }

        $form = $this->createForm(ProfilsType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // On récupère l'utilisateur connecté
            $profil->setUser($user); // On associe le profil à l'utilisateur

            // Gestion de l'image
            $picture = $form->get('picture')->getData(); // On récupère l'image dont entre autres la valeur qu'on lui a insérée

            // Le nom d'origine de l'image
            $originalPicture = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);

            // Sécurisation du nom du fichier
            $safePicture = $slugger->slug($originalPicture); // On utilise le slugger pour sécuriser le nom du fichier. Que fait il ? Le slugger remplace les caractères spéciaux par des tirets et met le nom en minuscules

            // On génère un nom unique pour l'image en ajoutant un identifiant unique
            $newPicture = $safePicture.'-'.uniqid().'.'.$picture->guessExtension(); // uniqid() génère un identifiant unique et guessExtension() permet de détecter l'extension du fichier

            // On déplace le fichier dans le répertoire de destination
            $picture->move(
                $this->getParameter('profile_pictures_directory'), // On récupère le répertoire de destination
                $newPicture // On déplace le fichier dans le répertoire de destination
            );

            $profil->setPicture($newPicture); // On associe le nom de l'image à l'entité Profils : mise à jour de la propriété picture dans $profil (object) avec le nouveau nom de l'image

            $entityManager->flush();

            return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
        }

        $categories = $categoriesRepository->findAll(); // On récupère toutes les catégories
        return $this->render('profils/edit.html.twig', [
            'profil' => $profil,
            'form' => $form,
            'categories' => $categories, // On passe les catégories récupérées à la vue
        ]);
    }

    #[Route('/{id}', name: 'app_profils_delete', methods: ['POST'])]
    public function delete(Request $request, Profils $profil, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profil->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($profil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
    }
}
