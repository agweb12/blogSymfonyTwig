<?php

namespace App\Controller\Admin;

use App\Entity\Profils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProfilsCrudController extends AbstractCrudController
{
    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security; 
    }

    public static function getEntityFqcn(): string
    {
        return Profils::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!$entityInstance instanceof Profils){
            return;
        }

        $user = $this->security->getUser();
        $entityInstance->setUser($user); 
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextareaField::new('descriptif'),
            ImageField::new('picture')->setBasePath('images/profils')
                ->setUploadDir('public/images/profils')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            DateField::new('date_birth', 'Date de naissance')
                ->setFormat('dd/MM/yyyy'), // Format de la date
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('user', 'Users')->hideOnForm()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute le bouton "Détails" sur la page d'index
    }
}
