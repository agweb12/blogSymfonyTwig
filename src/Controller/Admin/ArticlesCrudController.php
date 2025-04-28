<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticlesCrudController extends AbstractCrudController
{
    private Security $security; // C'est quoi Security ? C'est une classe de Symfony qui permet de gérer la sécurité de l'application. Elle permet de vérifier si l'utilisateur est authentifié ou non, et de récupérer les informations sur l'utilisateur connecté.

    public function __construct(Security $security) // C'est quoi le constructeur ? C'est une méthode spéciale qui est appelée lors de la création d'un objet de la classe. Il permet d'initialiser les propriétés de l'objet
    {
        $this->security = $security; // On initialise la propriété security avec l'objet Security passé en paramètre
    }

    public static function getEntityFqcn(): string
    {
        return Articles::class;
    }

    // EntityManagerInterface est une interface de Doctrine qui permet de gérer les entités et de les persister dans la base de données
    // persistEntity est une méthode qui permet de persister une entité dans la base de données
    // $entityManager est l'instance de EntityManagerInterface
    // $entityInstance est l'instance de l'entité à persister
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void // est appelée juste avant l'insertion de n'importe quel objet
    {
        // On vérifie que l'objet qu'on va insérer est bien un objet de la classe Articles
        if(!$entityInstance instanceof Articles){
            return; // Si l'entité n'est pas une instance de Articles, on ne fait rien
        }

        $user = $this->security->getUser(); // On récupère l'utilisateur connecté grâce au service Security de Symfony
        $entityInstance->setUser($user); // On associe l'utilisateur à l'article : on dit à l'article qui est l'utilisateur connecté qui a créé l'article
        // setUser est une méthode de la classe Articles qui permet d'associer un utilisateur à un article
        parent::persistEntity($entityManager, $entityInstance); // On appelle la méthode persistEntity de la classe parente (AbstractCrudController) pour persister l'entité dans la base de données
        // parent::persistEntity() est une méthode de la classe parente qui permet de persister l'entité dans la base de données
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(), // on cache l'id sur le formulaire et sur l'index dans le tableau
            TextField::new('title'),
            TextareaField::new('content'),
            ImageField::new('image')->setBasePath('images/articles') // Chemin d'accès à l'image
                ->setUploadDir('public/images/articles') // Répertoire de téléchargement de l'image c'est l echmin sur le serveur où easyAdmin va stocker les images stockées
                ->setUploadedFileNamePattern('[randomhash].[extension]'), // Modèle de nom de fichier téléchargé
            // setFormTypeOption('attr', ['accept' => 'image/*']), // Limiter le type de fichier à une image
            DateTimeField::new('createdAt')->hideOnForm(), // C'est quoi "::" ? C'est un opérateur de résolution de portée (scope resolution operator) en PHP. Il est utilisé pour accéder à des membres statiques, constants ou méthodes d'une classe.
            DateTimeField::new('updatedAt')->hideOnForm(),
            // Liste des catégories de l'article
            // Association ManyToMany avec la classe Categories
            // TextField::new('category.name'), // On cache le champ sur l'index
            // TextField::new('category')->setFormTypeOption('choice_label', 'name'), // On cache le champ sur l'index
            
            // 👇 Affichage en texte (juste sur l'index)
            // TextField::new('name', 'category')->onlyOnIndex(),
            AssociationField::new('category', 'category')->setFormTypeOption('choice_label', 'name')
                ->setLabel('Catégories')
                ->formatValue(function ($value, $entity) {
                // FormatValue est une méthode qui permet de formater la valeur d'un champ avant de l'afficher dans la liste
                // $value est la valeur du champ
                // $entity est l'entité courante
                // Récupère les noms des catégories
                return implode(', ', $entity->getCategory()->map(function($category) { // c'est quoi map ? 
                    // C'est quoi map () ? C'est une méthode de la classe Collection qui permet de transformer chaque élément d'une collection en un autre élément ? C'est à dire ? map permet de parcourir chaque élément de la collection et d'appliquer une fonction à chaque élément. La fonction renvoie le nom de la catégorie
                    // et la méthode toArray() permet de convertir la collection en tableau
                    return $category->getName();
                })->toArray());
            }),
            AssociationField::new('user', 'Users')->hideOnForm()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute le bouton "Détails" sur la page d'index
    }
}
