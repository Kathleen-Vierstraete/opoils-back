<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\Member;
use App\Entity\Hobby;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                // expanded false to fit the entity and the cardinality
                'expanded' => false,
                'label' => 'Propriétaire',
            ])

            // ->add('hobbies', TextType::class, [
                 //the related entity
                //  'class' => Hobby::class, 
                 //we want to display the member's pseudo to link it to the dog
                //  'choice_label' => 'hobby', 
                //  'mapped' => false,
                 //this is an array
                //  'multiple' =>true,
                 // expanded false to fit the entity and the cardinality
                //  'expanded' => false,             
            //     'label' => 'Les hobbys de votre chien',
            // ]) 

            // ->add('pictures', FileType::class, [
            //     'label' => 'Votre photo de profil',
            //     'mapped' => false,
            //     'required' => false,
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '1024k',
            //             'mimeTypes' => [
            //                 'image/jpg',
            //                 'image/jpeg',
            //             ],
            //             'mimeTypesMessage' => 'Merci de choisir un format d\'image valide',
            //         ])
            //     ]
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dog::class,
        ]);
    }
}
