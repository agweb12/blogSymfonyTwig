<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'attr' => [
                    'placeholder' => 'Entrez le titre de l\'article',
                    'class' => 'form-control',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de l\'article',
                'attr' => [
                    'placeholder' => 'Entrez le contenu de l\'article',
                    'class' => 'form-control',
                    'rows' => 10
                ],
            ])
            ->add('image', FileType::class, [
                'label' => "Image de l'article",
                // 'mapped' => false, // On ne mappe pas le champ avec l'entité Profils c'est à dire qu'on ne va pas l'enregistrer dans la base de données
                'data_class' => null,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Merci d\'insérer une image valide (JPEG, PNG, WEBP, GIF)',
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name', // Nom de la propriété à afficher dans le select
                'label' => 'Catégorie',
                'multiple' => true,
                'expanded' => true, // false pour un select, true pour des checkboxes
                
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary mt-2 mb-5',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
