<?php


namespace App\Form;

use App\Entity\CompanyProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Company Name',
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('logo', FileType::class, [
                'label' => 'Upload Logo',
                'mapped' => false,  // Champ non mappé à l'entité
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyProfile::class,
        ]);
    }
}
