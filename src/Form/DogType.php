<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\Hobby;
use App\Entity\Member;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Le nom de votre chien',
            ])

            ->add('age', TextType::class, [
                'label' => 'L\'âge de votre chien',
            ])

            ->add('race', TextType::class, [
                'label' => 'La race de votre chien',
            ])

            ->add('presentation', TextType::class, [
                'label' => 'Le présentation de votre chien',
            ])

            ->add('size', ChoiceType::class, [
                'choices' => [
                    'Petit' => 'Petit',
                    'Moyen' => 'Moyen',
                    'Grand' => 'Grand'
                ],
                'expanded' => true,

                'label' => 'La taille de votre chien',
            ])

            ->add('personality', ChoiceType::class, [
                'choices' => [
                    'Calme' => 'Calme',
                    'Adaptable' => 'Adaptable',
                    'Energique' => 'Energique'
                ],
                'expanded' => true,

                'label' => 'Le tempérament de votre chien',
            ])
            
            ->add('member', EntityType::class, [
                //the related entity
                'class' => Member::class, 
                //we want to display the member's pseudo to link it to the dog
                'choice_label' => 'pseudo', 
                //this is an array
                'multiple' =>false,
                // expanded false to fit the entity and the cardinality
                'expanded' => false,
                'label' => 'Propriétaire',
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dog::class,
        ]);
    }
}
