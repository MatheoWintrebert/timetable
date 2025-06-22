<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Niveau;
use App\Entity\Classe;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la classe (ex: 6A, Jupiter, Étoile)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'nom',
                'label' => 'Niveau',
                'placeholder' => 'Sélectionnez un niveau',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
