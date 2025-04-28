<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Profils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProfilsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', FileType::class, [
                'label' => "Photo de profil",
                'mapped' => false,
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
            ->add('descriptif' ,TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 10,
                    'placeholder' => 'Décrivez-vous en quelques mots'
                ]
            ])
            ->add('dateBirth', BirthdayType::class, [
                "input" => "datetime_immutable",
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profils::class,
        ]);
    }
}
