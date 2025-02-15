<?php

namespace App\Form;

use App\Entity\DeveloperProfile;
use App\Entity\Technology;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeveloperProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
            ])
            ->add('experienceLevel', NumberType::class, [
                'label' => 'Experience Level (Years)',
            ])
            ->add('minSalary', NumberType::class, [
                'label' => 'Minimum Salary',
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biography',
                'required' => false,
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Upload Avatar',
                'mapped' => false, // Champ non mappé à l'entité
                'required' => false,
            ])
            ->add('technologies', EntityType::class, [
                'class' => Technology::class,
                'label' => 'Technologies',
                'multiple' => true,
                'expanded' => true,  // Utilisé pour afficher les cases à cocher
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeveloperProfile::class,
        ]);
    }
}
