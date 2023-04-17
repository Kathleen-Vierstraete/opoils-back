<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\Hobby;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HobbyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hobby', TextType::class, [
                'label' => 'Le hobby de votre chien',
            ])
            ->add('dog', EntityType::class, [
                //the related entity
                'class' => Dog::class, 
                //we want to display the member's pseudo to link it to the dog
                'choice_label' => 'name', 
                //this is an array
                'multiple' =>false,
                // expanded false to fit the entity and the cardinality
                'expanded' => false,
                'label' => 'Nom du chien',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hobby::class,
        ]);
    }
}
