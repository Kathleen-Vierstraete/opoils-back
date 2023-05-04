<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')

            ->add('roles', ChoiceType::class, [
                'choices' => [
                    
                    'Membre' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôle(s)',
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ],
                ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // On récupère le form depuis l'event (pour travailler avec)
                $form = $event->getForm();
                // On récupère le user mappé sur le form depuis l'event
                $user = $event->getData();
    
                // On conditionne le champ "password"
                // Si user existant, il a id non null
                if ($user->getId() !== null) {
                    // Edit
                    $form->add('password', PasswordType::class, [
                        // Pour le form d'édition, on n'associe pas le password à l'entité
                        // @link https://symfony.com/doc/current/reference/forms/types/form.html#mapped
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé'
                        ]
                    ]);
                } else {
                    // New
                    $form->add('password', PasswordType::class, [
                        // En cas d'erreur du type
                        // Expected argument of type "string", "null" given at property path "password".
                        // (notamment à l'edit en cas de passage d'une valeur existante à vide)
                        'empty_data' => '',
                        // On déplace les contraintes de l'entité vers le form d'ajout
                        'constraints' => [
                            new NotBlank(),
                            new Regex(
                                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&€])[A-Za-z\d@$!%*?&€]{8,}$/",
                                "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                            ),
                        ],
                    ]);
                }
            })

            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
            ])

            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
            ])

            ->add('username', TextType::class, [
                'label' => 'Votre pseudo',
            ])

            ->add('presentation_member', TextareaType::class, [
                'label' => 'Votre présentation',
            ])            

            ->add('phone', TextType::class, [
                'label' => 'Votre numéro de téléphone',
            ])

            ->add('adress', TextType::class, [
                'label' => 'Votre adresse postale',
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Votre département',
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre ville',
            ])
            ->add('picture', FileType::class, [
                'label' => 'Votre photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci de choisir un format d\'image valide',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
