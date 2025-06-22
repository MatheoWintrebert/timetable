<?php declare(strict_types=1);

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;

class VoidTimeTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startHour', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'mapped' => false,
            ])
            ->add('courseDuration', IntegerType::class, [
                'label' => 'Durée du cours (en minutes)',
                'mapped' => false,
            ])
            ->add('breakDuration', IntegerType::class, [
                'label' => 'Durée de la pause (en minutes)',
                'mapped' => false,
            ])
            ->add('breakFrequency', IntegerType::class, [
                'label' => 'Fréquence des pauses (en nombres de cours)',
                'mapped' => false,
            ])
            ->add('lunchBreakDuration', IntegerType::class, [
                'label' => 'Durée de la pause déjeuner (en minutes)',
                'mapped' => false,
            ])
            ->add('endHour', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
    }
}
