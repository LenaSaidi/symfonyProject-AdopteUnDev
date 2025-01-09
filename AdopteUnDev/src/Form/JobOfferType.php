<?php

// src/Form/JobOfferType.php
namespace App\Form;

use App\Entity\JobOffer;
use App\Entity\Technology;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('location', TextType::class, ['label' => 'Location'])
            ->add('experienceRequired', IntegerType::class, [
                'label' => 'Years of Experience (optional)',
                'required' => false,
            ])
            ->add('salary', IntegerType::class, [
                'label' => 'Salary (optional)',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
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
            'data_class' => JobOffer::class,
        ]);
    }
}

