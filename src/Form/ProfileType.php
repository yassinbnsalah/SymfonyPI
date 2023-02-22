<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('CIN',IntegerType::class, [
                'empty_data' => ' '
            ])
            ->add('Name', null, [
                'empty_data' => ''
            ])
            ->add('Numero')
            ->add('Age', IntegerType::class, [
                'empty_data' => ' '
            ])
            ->add('Adresse')
            ->add('image',FileType::class,[
                'mapped' => false,
                'required' => false,
                'data' => '',
                'constraints' => [
                    new File([
                        'maxSize' => '2Mi',
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
                
            ])
            ->add('save' ,  SubmitType::class)
        ;
    
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
