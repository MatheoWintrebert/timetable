<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Repository\ProgrammeRepository;
use App\Form\ClasseType;
use App\Entity\VoidTimeTable;
use App\Entity\Programme;
use App\Entity\Professeur;
use App\Entity\Classe;

class ClasseController extends AbstractController
{
    private OngletService $ongletService;
    public function __construct(OngletService $ongletService)
    {
        $this->ongletService = $ongletService;
    }

    // Route principale pour afficher l'index
    #[Route(path: '/classe', name: 'app_classe')]
    public function index(): Response
    {
        return $this->render(view: 'classe/index.html.twig');
    }

    // Route dynamique pour afficher l'onglet (nouveau ou liste)
    #[Route('/classe/onglet/{nomOnglet}', name: 'classe_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        // Enregistrer l'onglet actif dans la session
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);

        // Définir les variables de base
        $routeName = 'classe_show';
        $context = 'app_classe';
        $classe = new Classe();
        $form = null;

        // Si l'onglet est "new", afficher le formulaire
        if ($nomOnglet === "new") {
            $form = $this->createForm(ClasseType::class, $classe);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($classe);
                $entityManager->flush();

                // Optionnel : Réinitialiser le form après succès
                $classe = new Classe();
                $form = $this->createForm(ClasseType::class, $classe);

                $this->addFlash('success', 'Classe créée avec succès !');
                return $this->render('_partials/flash.stream.twig', [], new Response('', 200, [
                    'Content-Type' => 'text/vnd.turbo-stream.html',
                ]));
            }

            $data['form'] = $form->createView();

            return $this->render('classe/onglet/new.html.twig', $data);
        }


        // Définir les données à passer à la vue
        $data = [
            'routeName' => $routeName,
            'nomOnglet' => $nomOnglet,
            'context' => $context,
        ];

        // Si l'onglet est "list", afficher la liste des classes
        if ($nomOnglet === "list") {
            $data['classes'] = $entityManager->getRepository(Classe::class)->findAll();
        }

        // Si l'onglet est inconnu, rediriger ou afficher une erreur
        return $this->render(
            view: 'classe/onglet/' . $nomOnglet . '.html.twig',
            parameters:
            $data
        );
    }

    #[Route('/classe/delete/{id}', name: 'classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_classe_' . $classe->getId(), $request->request->get('_token'))) {
            $em->remove($classe);
            $em->flush();
        }

        // Re-render partiel (liste des classes)
        $classes = $em->getRepository(Classe::class)->findAll();
        return $this->render('classe/onglet/list.html.twig', [
            'classes' => $classes,
            'nomOnglet' => 'list',
            'routeName' => 'classe_show',
            'context' => 'app_classe'
        ]);
    }

    #[Route('/classe/edt/{id}', name: 'classe_edt', methods: ['POST'])]
    public function classe_edt(Classe $classe, EntityManagerInterface $em, $id, Request $request): Response
    {
        $classe = $em->getRepository(Classe::class)->find($id);
        $edtClasse = $classe->getEdtClasse();

        if (empty($edtClasse)) {
            $voidTimeTable = $em->getRepository(VoidTimeTable::class)->findOneBy([]);

            if (!$voidTimeTable) {
                throw new \Exception('Aucun VoidTimeTable trouvé.');
            }

            $classe->setEdtClasse($voidTimeTable->getTimetable());
            $em->persist($classe);
            $em->flush();

            $edtClasse = $classe->getEdtClasse();
        }

        $niveau = $classe->getNiveau();
        $programmeArray = [];

        $programmes = $em->getRepository(Programme::class)->findBy(['niveau' => $niveau]);
        $tempsDispoProfesseurs = [];

        foreach ($programmes as $programme) {
            $matiere = $programme->getMatiere();
            $nbHeuresMatiere = $programme->getNbHeures();
            $professeursPotentiels = [];

            foreach ($matiere->getProfesseurs() as $professeur) {
                $preferences = $professeur->getPreferenceHoraire();

                if (empty($preferences)) {
                    $this->addFlash('warning', "Le professeur {$professeur->getNom()} {$professeur->getPrenom()} n'a pas défini ses préférences horaires.");
                    return $this->render('_partials/flash.stream.twig', [], new Response('', 200, [
                        'Content-Type' => 'text/vnd.turbo-stream.html',
                    ]));
                }

                if (empty($professeur->getEdtActuel())) {
                    $professeur->setEdtActuel($preferences);
                    $em->persist($professeur);
                }

                $edtActuel = $professeur->getEdtActuel();

                $totalOui = 0;
                $totalMeh = 0;

                foreach ($edtClasse as $jour => $creneaux) {
                    foreach ($creneaux as $creneau) {
                        if (($creneau['course'] ?? null) !== null) {
                            continue;
                        }

                        $horaire = $creneau['start'] . '-' . $creneau['end'];
                        $pref = $edtActuel[$jour][$horaire] ?? 'OUI';

                        if ($pref === 'OUI') {
                            $totalOui++;
                        } elseif ($pref === 'MEH') {
                            $totalMeh++;
                        }
                    }
                }

                $professeursPotentiels[] = [
                    'professeur' => $professeur,
                    'oui' => $totalOui,
                    'meh' => $totalMeh
                ];
            }

            $professeursDispo = array_filter($professeursPotentiels, fn($p) => $p['oui'] >= $nbHeuresMatiere);

            $useMeh = false;

            if (empty($professeursDispo)) {
                $professeursDispo = array_filter($professeursPotentiels, fn($p) => ($p['oui'] + $p['meh']) >= $nbHeuresMatiere);
                $useMeh = true;

                if (empty($professeursDispo)) {
                    $this->addFlash('danger', "Aucun professeur n'a suffisamment d'heures disponibles pour la matière {$matiere->getNom()}.");
                    return $this->render('_partials/flash.stream.twig', [], new Response('', 200, [
                        'Content-Type' => 'text/vnd.turbo-stream.html',
                    ]));

                }
            }

            $choixUnique = false;
            if (count($professeursDispo) === 1) {
                $profChoisiData = array_values($professeursDispo)[0];
                $profChoisi = $profChoisiData['professeur'];
                $choixUnique = true;
            } else {
                foreach ($professeursDispo as $profDispo) {
                    if ($profDispo['oui'] === $nbHeuresMatiere) {
                        $profChoisiData = $profDispo;
                        $profChoisi = $profDispo['professeur'];
                        $choixUnique = true;
                        break;
                    }
                }
            }

            if (!$choixUnique) {
                $randomIndex = random_int(0, count($professeursDispo) - 1);
                $profChoisiData = array_values($professeursDispo)[$randomIndex];
                $profChoisi = $profChoisiData['professeur'];
            }

            $totalDisponibles = $profChoisiData['oui'] + ($useMeh ? $profChoisiData['meh'] : 0);
            $tempsRestant = $totalDisponibles - $nbHeuresMatiere;
            $tempsDispoProfesseurs[$profChoisi->getId()] = $tempsRestant;

            $programmeArray[] = [
                'matiere' => $matiere,
                'nb_heures' => $nbHeuresMatiere,
                'profChoisi' => $profChoisi,
                'tempsRestant' => $tempsRestant,
                'useMeh' => $useMeh,
            ];
        }

        usort($programmeArray, function ($a, $b) {
            return $a['tempsRestant'] <=> $b['tempsRestant'];
        });

        foreach ($programmeArray as &$programme) {
            $matiere = $programme['matiere'];
            $nbHeures = $programme['nb_heures'];
            $profChoisi = $programme['profChoisi'];
            $useMeh = $programme['useMeh'];

            $edtProf = $profChoisi->getEdtActuel();

            for ($i = 0; $i < $nbHeures; $i++) {
                $placed = $this->placerMatiereDansEdt($edtProf, $edtClasse, $matiere->getNom(), $useMeh);
                if (!$placed) {
                    $this->addFlash('warning', "Impossible de placer toute la matière {$matiere->getNom()} pour le professeur {$profChoisi->getNom()}.");
                    return $this->render('_partials/flash.stream.twig', [], new Response('', 200, [
                        'Content-Type' => 'text/vnd.turbo-stream.html',
                    ]));

                }
            }

            $profChoisi->setEdtActuel($edtProf);
            $em->persist($profChoisi);
        }

        $classe->setEdtClasse($edtClasse);
        $em->persist($classe);

        $em->flush();

        $classes = $em->getRepository(Classe::class)->findAll();

        return $this->render('classe/onglet/list.html.twig', [
            'classes' => $classes,
            'nomOnglet' => 'list',
            'routeName' => 'classe_show',
            'context' => 'app_classe',
            'programmeArray' => $programmeArray,
        ]);
    }

    /**
     * Place la matière dans l'emploi du temps du prof et de la classe, à un créneau aléatoire disponible.
     * Priorité aux créneaux 'OUI', puis 'MEH' si $useMeh = true.
     *
     * @param array &$edtProf Emploi du temps du professeur (modifié par référence)
     * @param array &$edtClasse Emploi du temps de la classe (modifié par référence)
     * @param string $nomMatiere Nom de la matière à placer
     * @param bool $useMeh Si true, autorise les créneaux 'MEH' aussi
     * @return bool True si placement réussi, false sinon
     */
    private function placerMatiereDansEdt(array &$edtProf, array &$edtClasse, string $nomMatiere, bool $useMeh): bool
    {
        $jours = array_keys($edtProf);

        $creneauxLibres = [];

        foreach ($jours as $jour) {
            foreach ($edtProf[$jour] as $index => $creneau) {
                $courseClasse = $edtClasse[$jour][$index]['course'] ?? null;
                $courseProf = $creneau['course'] ?? null;

                if ($courseClasse === null && ($courseProf === 'OUI' || ($useMeh && $courseProf === 'MEH'))) {
                    $creneauxLibres[] = [
                        'jour' => $jour,
                        'index' => $index,
                    ];
                }
            }
        }

        if (empty($creneauxLibres)) {
            return false;
        }

        $choix = $creneauxLibres[array_rand($creneauxLibres)];

        $jourChoisi = $choix['jour'];
        $indexChoisi = $choix['index'];

        $edtProf[$jourChoisi][$indexChoisi]['course'] = $nomMatiere;

        $edtClasse[$jourChoisi][$indexChoisi]['course'] = $nomMatiere;

        return true;
    }

    #[Route('/classe/edt/show/{id}', name: 'show_edt', methods: ['POST', 'GET'])]
    public function showEdt(Classe $classe, ProgrammeRepository $programmeRepository): Response
    {
        $edtClasse = $classe->getEdtClasse();

        $subjectColors = [];
        $niveau = $classe->getNiveau();
        $programmes = $programmeRepository->findBy(['niveau' => $niveau]);

        foreach ($programmes as $programme) {
            foreach ($programmes as $programme) {
                $matiere = $programme->getMatiere();
                $subjectColors[$matiere->getNom()] = $matiere->getCouleur();
            }
        }
        $subjectColors['lunchBreak'] = '#DBAD6A';

        return $this->render('classe/onglet/edt_show.html.twig', [
            'classe' => $classe,
            'edtClasse' => $edtClasse,
            'subjectColors' => $subjectColors,
        ]);
    }
}
