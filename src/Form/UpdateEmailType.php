<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentEmail', EmailType::class, [
                'label' => 'Votre adresse mail',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une adresse email valide']),
                ]
            ])
            ->add('newEmail', EmailType::class, [
                'label' => 'Votre adresse mail',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une adresse email valide']),
                ]
            ])
            ->add('confirmEmail', EmailType::class, [
                'label' => 'Confirmation du adresse mail',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une adresse email valide']),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
