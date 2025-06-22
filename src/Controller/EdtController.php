<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Professeur;
use App\Entity\Classe;

final class EdtController extends AbstractController
{
    #[Route('/professeur/emploi-du-temps/{id}', name: 'app_edt')]
    public function showEdt(int $id, EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
        // Récupérer le professeur par son ID
        $professeur = $entityManager->getRepository(Professeur::class)->find($id);

        if (!$professeur) {
            throw $this->createNotFoundException('Le professeur n\'a pas été trouvé');
        }

        // Récupérer toutes les classes
        $classes = $entityManager->getRepository(Classe::class)->findAll();

        // Récupérer l'emploi du temps depuis la session, s'il existe
        $emploiDuTemps = $session->get('emploi_du_temps', null);

        // Si l'EDT n'existe pas dans la session et que le bouton n'est pas cliqué, on ne fait rien
        if (!$emploiDuTemps && !$request->get('generate')) {
            $emploiDuTemps = $this->generateEmploiDuTemps($classes);
            // Sauvegarder l'EDT dans la session
            $session->set('emploi_du_temps', $emploiDuTemps);
        }

        // Initialiser $emploiDuTemps si jamais il n'est toujours pas défini
        if (!$emploiDuTemps) {
            $emploiDuTemps = [];
        }

        return $this->render('professeur/emploi_du_temps.html.twig', [
            'professeur' => $professeur,
            'emploiDuTemps' => $emploiDuTemps,
        ]);
    }

    #[Route('/professeur/generer-emploi-du-temps/{id}', name: 'app_edt_generate', methods: ['POST'])]
    public function generateEdt(int $id, EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
        // Récupérer le professeur par son ID
        $professeur = $entityManager->getRepository(Professeur::class)->find($id);

        if (!$professeur) {
            throw $this->createNotFoundException('Le professeur n\'a pas été trouvé');
        }

        // Récupérer toutes les classes
        $classes = $entityManager->getRepository(Classe::class)->findAll();

        // Générer un nouvel emploi du temps uniquement si le bouton est cliqué
        if ($request->get('generate')) {
            $emploiDuTemps = $this->generateEmploiDuTemps($classes);
            // Sauvegarder l'EDT dans la session
            $session->set('emploi_du_temps', $emploiDuTemps);
        }

        return $this->render('professeur/emploi_du_temps.html.twig', [
            'professeur' => $professeur,
            'emploiDuTemps' => $emploiDuTemps,
        ]);
    }

    // Fonction pour générer un emploi du temps sans conflit
    private function generateEmploiDuTemps(array $classes): array
    {
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        $horaires = range(8, 17); // 10 créneaux par jour

        // Créer tous les créneaux disponibles
        $allSlots = [];
        foreach ($jours as $jour) {
            foreach ($horaires as $heure) {
                $allSlots[] = ['jour' => $jour, 'heure' => $heure];
            }
        }

        // Mélanger les créneaux
        shuffle($allSlots);

        $emploiDuTemps = [];
        foreach ($jours as $jour) {
            foreach ($horaires as $heure) {
                $emploiDuTemps[$jour][$heure] = null;
            }
        }

        // Assigner chaque classe à un créneau sans conflit
        foreach ($classes as $index => $classe) {
            if (!isset($allSlots[$index])) {
                break;
            }

            $slot = $allSlots[$index];
            $emploiDuTemps[$slot['jour']][$slot['heure']] = $classe;
        }

        return $emploiDuTemps;
    }
}

