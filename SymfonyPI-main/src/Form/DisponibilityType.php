<?php

namespace App\Form;

use App\Entity\Disponibility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisponibilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDispo', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('heureStart')
            ->add('heureEnd')
            ->add('Note')
            ->add('save' , SubmitType::class)
         //   ->add('doctor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Disponibility::class,
        ]);
    }
}
