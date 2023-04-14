<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            
            ->add('member', EntityType::class, [
                //the related entity
                'class' => Member::class, 
                //we want to display the member's pseudo to link it to the dog
                'choice_label' => 'pseudo', 
                //this is an array
                'multiple' =>false,
                // expanded true to have checkboxes
                'expanded' => false,
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
