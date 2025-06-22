<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\Niveau;
use App\Entity\Programme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'nom',
                'label' => 'Niveau',
                'placeholder' => 'Sélectionnez un niveau',
                'attr' => ['class' => 'form-control']
            ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'nom',
                'label' => 'Matière',
                'placeholder' => 'Sélectionnez une matière',
                'attr' => ['class' => 'form-control']
            ])
            ->add('nbHeures', IntegerType::class, [
                'label' => 'Nombre d\'heures par semaine',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
