<?php

namespace App\Service;

use DateTime;
use DateInterval;

class EmploiDuTempsGenerator
{
    public function generer(array $formData): array
    {
        $start = new DateTime($formData['startHour']);
        $end = new DateTime($formData['endHour']);
        $courseDuration = (int) $formData['courseDuration'];
        $breakDuration = (int) $formData['breakDuration'];
        $breakFrequency = (int) $formData['breakFrequency'];
        $lunchBreakStart = new DateTime($formData['lunchBreakStart']);
        $lunchBreakDuration = (int) $formData['lunchBreakDuration'];
        $lunchBreakEnd = (clone $lunchBreakStart)->add(new DateInterval('PT' . $lunchBreakDuration . 'M'));

        $current = clone $start;
        $counter = 0;
        $timeSlots = [];

        while ($current < $end) {
            $slotStart = clone $current;
            $slotEnd = (clone $current)->add(new DateInterval('PT' . $courseDuration . 'M'));

            // Gérer chevauchement avec la pause déjeuner
            if ($slotStart < $lunchBreakEnd && $slotEnd > $lunchBreakStart) {
                $current = clone $lunchBreakEnd;
                continue;
            }

            if ($slotEnd > $end) {
                break;
            }

            $timeSlots[] = [
                'start' => $slotStart->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'course' => null,
            ];

            $current = clone $slotEnd;
            $counter++;

            // Pause régulière
            if ($breakFrequency > 0 && $counter % $breakFrequency === 0) {
                $current->add(new DateInterval('PT' . $breakDuration . 'M'));
            }
        }
        $timeSlots[] = [
            'start' => $lunchBreakStart->format('H:i'),
            'end' => $lunchBreakEnd->format('H:i'),
            'course' => 'lunchBreak',
        ];
        usort($timeSlots, function ($a, $b) {
            return strcmp($a['start'], $b['start']);
        });
        // Répartition sur les jours de la semaine
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        $emploiDuTemps = [];
        foreach ($days as $day) {
            $emploiDuTemps[$day] = $timeSlots;
        }

        return $emploiDuTemps;
    }
}
