<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Service\EmploiDuTempsGenerator;
use App\Entity\VoidTimeTable;

class VoidTableController extends AbstractController
{
    private OngletService $ongletService;
    private EmploiDuTempsGenerator $edtGenerator;
    public function __construct(OngletService $ongletService, EmploiDuTempsGenerator $edtGenerator)
    {
        $this->ongletService = $ongletService;
        $this->edtGenerator = $edtGenerator;
    }

    // Route principale pour afficher l'index
    #[Route(path: '/void_table', name: 'app_emploi')]
    public function index(): Response
    {
        return $this->render(view: 'void_table/index.html.twig');
    }

    // Route dynamique pour afficher l'onglet (nouveau ou liste)
    #[Route('/void_table/onglet/{nomOnglet}', name: 'emploi_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        // Enregistrer l'onglet actif dans la session
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);

        // Définir les variables de base
        $routeName = 'emploi_show';
        $context = 'app_emploi';
        $form = null;
        $data = [
            'routeName' => $routeName,
            'nomOnglet' => $nomOnglet,
            'context' => $context,
        ];

        // Si l'onglet est "create", afficher et traiter le formulaire
        if ($nomOnglet === 'create' && $request->isMethod('POST')) {
            $formData = [
                'startHour' => $request->request->get('startHour'),
                'courseDuration' => $request->request->get('courseDuration'),
                'breakDuration' => $request->request->get('breakDuration'),
                'breakFrequency' => $request->request->get('breakFrequency'),
                'lunchBreakStart' => $request->request->get('lunchBreakStart'),
                'lunchBreakDuration' => $request->request->get('lunchBreakDuration'),
                'endHour' => $request->request->get('endHour'),
            ];

            // Valider manuellement ici si besoin

            $request->getSession()->set('void_form_data', $formData);
            $this->addFlash('success', 'Formulaire soumis manuellement !');

            $emploiDuTemps = $this->edtGenerator->generer($formData);
            $request->getSession()->set('voidEdt', $emploiDuTemps);

            $repo = $entityManager->getRepository(VoidTimeTable::class);
            $existing = $repo->findOneBy([]); // récupère le premier (il n'y en aura qu'un)

            if ($existing) {
                $entityManager->remove($existing);
                $entityManager->flush(); // Important pour éviter conflits
            }
            $voidTimeTable = new VoidTimeTable();
            $voidTimeTable->setTimetable($emploiDuTemps);
            $entityManager->persist($voidTimeTable);
            $entityManager->flush();
            return $this->redirectToRoute('emploi_show', ['nomOnglet' => 'show']);
        }


        // Si l'onglet est "show", afficher les données de session
        if ($nomOnglet === "show") {
            // Récupérer l'emploi du temps depuis la base de données
            $voidTimeTable = $entityManager
                ->getRepository(VoidTimeTable::class)
                ->findOneBy([]); // Un seul emploi du temps attendu

            $orderedDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
            $orderedEdt = [];

            foreach ($orderedDays as $day) {
                if (isset($voidTimeTable->getTimetable()[$day])) {
                    $orderedEdt[$day] = $voidTimeTable->getTimetable()[$day];
                }
            }

            $data['voidEdt'] = $orderedEdt;
        }

        // Rendu de la vue avec les données préparées
        return $this->render(
            view: 'void_table/onglet/' . $nomOnglet . '.html.twig',
            parameters: $data
        );
    }


}
