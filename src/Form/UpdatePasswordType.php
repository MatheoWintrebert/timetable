<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner votre mot de passe']),
                ]
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner votre mot de passe']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Mot de passe trop court',
                    ])
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmation mot de passe',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner votre mot de passe']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Mot de passe trop court',
                    ])
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
