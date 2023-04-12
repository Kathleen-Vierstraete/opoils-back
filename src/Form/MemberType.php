<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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

            ->add('password', PasswordType::class, [
                'help' => 'Make sure it\'s at least 8 characters including a number and a lowercase letter and a special character.',
                'constraints' => [
                    new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-+]).{8,}$/'),
                ]
            ])

            ->add('lastname', TextType::class, [
                'label' => 'votre nom',
            ])

            ->add('firstname', TextType::class, [
                'label' => 'votre prénom',
            ])

            ->add('username', TextType::class, [
                'label' => 'votre pseudo',
            ])

            ->add('phone', TextType::class, [
                'label' => 'votre numéro de téléphone',
            ])

            ->add('adress', TextType::class, [
                'label' => 'votre adresse postale',
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'votre code postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'votre ville',
            ])
            ->add('picture', FileType::class, [
                'label' => 'votre photo de profil',
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
