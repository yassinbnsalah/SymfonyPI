<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
           // ->add('DateRV', DateType::class, [
           //     'widget' => 'single_text',
          //  ])
           ->add('note',TextareaType::class, [
            'empty_data' => ''
        ])
            // ->add('DatePassageRV')
            // ->add('HourPassageRV')
            // ->add('State')
            // ->add('fromuser')
            // ->add('todoctor')
            ->add('save' ,  SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
