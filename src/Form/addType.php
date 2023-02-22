<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class addType extends AbstractType
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
        ->add('Email', EmailType::class, [
            'empty_data' => ''
        ])
        ->add('Adresse', null, [
            'empty_data' => ''
        ])
        ->add('Password', PasswordType::class, [
            'empty_data' => ''
        ])
        ->add('confirm_password', PasswordType::class, [
            'empty_data' => ''
        ])
        ->add('save', SubmitType::class);
    
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
