<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VoidTimeTableRepository;

final class SaveAvailabilitiesController extends AbstractController
{
    #[Route('/save_availabilities', name: 'app_save_availabilities', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, VoidTimeTableRepository $vttr): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $prof = $user->getProfesseur();

        if (!$prof) {
            throw $this->createAccessDeniedException('Aucun professeur associé à cet utilisateur.');
        }

        $timeTable = $vttr->findOneBy([]); // L’EDT complet à jour

        if ($request->isMethod('POST')) {
            $disposSoumises = $request->request->all('dispos');
            $disposCompletes = [];

            foreach ($timeTable->getTimetable() as $jour => $creneaux) {
                foreach ($creneaux as $creneau) {
                    $start = $creneau['start'];
                    $end = $creneau['end'];
                    $horaire = "$start-$end";

                    // Ne pas enregistrer les pauses déjeuner
                    if (($creneau['course'] ?? null) === 'lunchBreak') {
                        $disposCompletes[$jour][] = [
                            'start' => $start,
                            'end' => $end,
                            'course' => 'lunchBreak'
                        ];
                        continue;
                    }

                    // Par défaut, la valeur est OUI
                    $valeur = $disposSoumises[$jour][$horaire] ?? 'OUI';

                    $disposCompletes[$jour][] = [
                        'start' => $start,
                        'end' => $end,
                        'course' => $valeur // OUI, NON, MEH
                    ];
                }
            }

            $prof->setPreferenceHoraire($disposCompletes);
            $em->flush();

            $this->addFlash('success', 'Disponibilités enregistrées !');

            return $this->redirectToRoute('app_save_availabilities');
        }

        return $this->render('save_availabilities/index.html.twig', [
            'professeur' => $prof,
            'edt' => $timeTable
        ]);
    }
}
