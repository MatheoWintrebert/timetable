<?php

// src/Form/ProfesseurType.php

namespace App\Form;

use App\Entity\Professeur;
use App\Entity\Matiere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProfesseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('nbHeuresMax', IntegerType::class, [
                'label' => 'Nombre d\'heures maximum',
                'attr' => ['class' => 'form-control']
            ])
            ->add('matieres', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'nom',  // Assurez-vous que la propriété 'nom' existe dans Matiere
                'label' => 'Matières',
                'multiple' => true,  // Permet de sélectionner plusieurs matières
                'expanded' => true,  // Utilisation de cases à cocher pour chaque matière
                'placeholder' => 'Sélectionnez une ou plusieurs matières',
                'attr' => ['class' => 'form-control']
            ])
            // Pas d'ajout pour preferenceHoraire car tu ne veux pas le rendre visible dans le formulaire
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Professeur::class,
        ]);
    }
}

