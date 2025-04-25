<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Profils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit avoir au minimum {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Votre prénom ne doit pas dépasser {{ limit }} caractères'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Votre prénom ne doit contenir que des lettres'
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez votre nom',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit avoir au minimum {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Votre nom ne doit pas dépasser {{ limit }} caractères'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Votre nom ne doit contenir que des lettres'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre email',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre email'
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Votre email ne doit pas dépasser {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                        'message' => 'Veuillez entrer un email valide'
                    ])
                ]
            ])
            // ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez votre mot de passe',
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer votre mot de passe'
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit avoir au minimum {{ limit }} caractères',
                            'max' => 20,
                            'maxMessage' => 'Votre mot de passe ne doit pas dépasser {{ limit }} caractères'
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation du Mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmer votre mot de passe',
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez confirmer votre mot de passe'
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit avoir au minimum {{ limit }} caractères',
                            'max' => 20,
                            'maxMessage' => 'Votre mot de passe ne doit pas dépasser {{ limit }} caractères'
                        ])
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        //     ->add('profil', EntityType::class, [
        //         'class' => Profils::class,
        //         'choice_label' => 'id',
        //     ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
