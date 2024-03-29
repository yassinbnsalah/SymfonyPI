<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSub', DateType::class, [
                'widget' => 'single_text',
            ])
            //->add('dateExpire')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'One month' => "1",
                    '3 months' => "2",
                    '6 months' => "3",
                ],
            ])
            ->add('paiementType', ChoiceType::class, [
                'choices'  => [
                    'Cash' => "Cash",
                    'Cheque' => "Cheque",
                    'En ligne' => "En ligne",
                ],
            ])
            ->add('amount', TextType::class)
            ->add('save' , SubmitType::class)
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
