<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $entityManager): Response // mécanisme d'injection de dépendance : Symfony va automatiquement créer une instance de la classe Request et l'injecter dans la méthode index() lorsque cette route est appelée. Cela permet d'accéder facilement aux données de la requête HTTP, comme les paramètres GET ou POST, les fichiers téléchargés, etc. 
    {
        $user = new Users; // On crée un objet de la classe users et on le stocke dans la variable $user
        $form = $this->createForm(RegisterType::class, $user); // cette méthode prend en paramètres la classe du formulaire et l'objet gérer par le formulaire.
        $form->handleRequest($request); // On gère la requête HTTP pour le formulaire. Cela permet de lier les données du formulaire à l'objet $user. La méthode handleRequest() vérifie si le formulaire a été soumis et valide les données. Si le formulaire est soumis et valide, on peut ensuite enregistrer l'utilisateur dans la base de données.
        // il faut que mon formulaire écoute et analyse la requête qui vient de la vue et vérifier s'il y a un post envoyé ou pas
        // on utilise l'objet request créé par symfony et qui représente la requête HTTP entrante (ici la requête contient des données de formulaire)
        // On vérifie si le formulaire a été soumis et est valide
        if($form->isSubmitted() && $form->isValid()) { // isSubmitted() vérifie si le formulaire a été soumis et isValid() vérifie si les données du formulaire sont valides selon les contraintes définies dans la classe RegisterType.
            // il faut hasher le mot de passe avant de l'enregistrer dans la base de données
            $password = $form->get('password')->getData(); // On récupère le mot de passe du formulaire
            $passwordHashed = $userPasswordHasherInterface->hashPassword($user, $password);
            $user->setPassword($passwordHashed); // On hache le mot de passe et on l'associe à l'utilisateur

            $entityManager->persist($user); // On prépare l'entité pour l'enregistrement dans la base de données
            $entityManager->flush(); // On enregistre l'utilisateur dans la base de données
            // $this->addFlash('success', 'Votre compte a bien été créé !'); // On ajoute un message flash pour informer l'utilisateur que son compte a été créé avec succès. addFlash() est une méthode de la classe AbstractController qui permet d'ajouter un message flash à la session. Ces messages sont généralement utilisés pour afficher des notifications à l'utilisateur après une action, comme la soumission d'un formulaire ou la connexion.
            return $this->redirectToRoute('app_login'); // On redirige l'utilisateur vers la page de connexion après l'inscription réussie. redirectToRoute() est une méthode de la classe AbstractController qui permet de rediriger l'utilisateur vers une autre route définie dans l'application Symfony.
        }
        return $this->render('register/register.html.twig', [ // C'est quoi render() exactement ? render() est une méthode de la classe AbstractController qui permet de rendre une vue Twig. Elle prend en paramètres le nom du fichier de la vue et un tableau associatif contenant les variables à passer à la vue.
            'titleH1' => 'Formulaire d\'inscription',
            'formInscription' => $form->createView() // créer la vue du formulaire
        ]);
    }
}
