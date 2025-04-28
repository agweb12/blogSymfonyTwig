<?php

namespace App\Controller;

use App\Entity\Profils;
use App\Form\ProfilsType;
use App\Repository\ProfilsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
final class ProfilsController extends AbstractController
{
    #[Route(name: 'app_profils_index', methods: ['GET'])]
    public function index(ProfilsRepository $profilsRepository): Response
    {
        $user = $this->getUser(); // On récupère l'utilisateur connecté
        return $this->render('account/account.html.twig', [
            'profil' => $profilsRepository->findByUser($user) // On utilise la méthode findByUser pour récupérer le profil de l'utilisateur
        ]);
    }

    // Cela crée un nouveau profil
    // On utilise le formulaire ProfilsType pour créer un nouveau profil
    #[Route('/new', name: 'app_profils_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profil = new Profils();
        $form = $this->createForm(ProfilsType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // On récupère l'utilisateur connecté
            $profil->setUser($user); // On associe le profil à l'utilisateur
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profils/new.html.twig', [
            'profil' => $profil,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profils_show', methods: ['GET'])]
    public function show(Profils $profil): Response
    {
        return $this->render('account/show.html.twig', [
            'profil' => $profil,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profils_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profils $profil, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfilsType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profils/edit.html.twig', [
            'profil' => $profil,
            'form' => $form,
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
