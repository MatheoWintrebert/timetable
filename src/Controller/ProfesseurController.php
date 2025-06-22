<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Form\ProfesseurType;
use App\Entity\User;
use App\Entity\Programme;
use App\Entity\Professeur;
use App\Entity\Cours;

class ProfesseurController extends AbstractController
{
    private OngletService $ongletService;
    public function __construct(OngletService $ongletService)
    {
        $this->ongletService = $ongletService;
    }

    // Route principale pour afficher l'index
    #[Route(path: '/professeur', name: 'app_professeur')]
    public function index(): Response
    {
        return $this->render(view: 'professeur/index.html.twig');
    }

    // Route dynamique pour afficher l'onglet (nouveau ou liste)
    #[Route('/professeur/onglet/{nomOnglet}', name: 'professeur_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        // Enregistrer l'onglet actif dans la session
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);

        // Définir les variables de base
        $routeName = 'professeur_show';
        $context = 'app_professeur';
        $professeur = new Professeur();
        $form = null;

        // Si l'onglet est "new", afficher le formulaire
        if ($nomOnglet === "new") {
            $form = $this->createForm(ProfesseurType::class, $professeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($professeur);
                $entityManager->flush();

                // Optionnel : Réinitialiser le form après succès
                $professeur = new Professeur();
                $form = $this->createForm(ProfesseurType::class, $professeur);

                $this->addFlash('success', 'Professeur créée avec succès !');
            }

            $data['form'] = $form->createView();

            return $this->render('professeur/onglet/new.html.twig', $data);
        }

        if ($nomOnglet === "assign") {
            // Récupération des données
            $users = $entityManager->getRepository(User::class)->findAll();
            $professeurs = $entityManager->getRepository(Professeur::class)->findAll();

            // Traitement du POST
            if ($request->isMethod('POST')) {
                $userId = $request->request->get('user');
                $profId = $request->request->get('professeur');

                $user = $entityManager->getRepository(User::class)->find($userId);
                $prof = $entityManager->getRepository(Professeur::class)->find($profId);

                if ($user && $prof) {
                    $user->setProfesseur($prof); // via relation ManyToOne ou juste ID
                    $entityManager->flush();
                    $this->addFlash('success', 'Utilisateur assigné au professeur avec succès.');
                } else {
                    $this->addFlash('error', 'Erreur lors de l’assignation.');
                }
            }

            return $this->render('professeur/onglet/assign.html.twig', [
                'users' => $users,
                'professeurs' => $professeurs,
                'nomOnglet' => 'assign',
                'routeName' => 'professeur_show',
                'context' => 'app_professeur',
            ]);
        }


        // Définir les données à passer à la vue
        $data = [
            'routeName' => $routeName,
            'nomOnglet' => $nomOnglet,
            'context' => $context,
        ];

        // Si l'onglet est "list", afficher la liste des professeurs
        if ($nomOnglet === "list") {
            $data['professeurs'] = $entityManager->getRepository(Professeur::class)->findAll();
        }

        // Si l'onglet est inconnu, rediriger ou afficher une erreur
        return $this->render(
            view: 'professeur/onglet/' . $nomOnglet . '.html.twig',
            parameters:
            $data
        );
    }

    #[Route('/professeur/delete/{id}', name: 'professeur_delete', methods: ['POST'])]
    public function delete(Request $request, Professeur $professeur, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_professeur_' . $professeur->getId(), $request->request->get('_token'))) {
            $em->remove($professeur);
            $em->flush();
        }

        // Re-render partiel (liste des professeurs)
        $professeurs = $em->getRepository(Professeur::class)->findAll();
        return $this->render('professeur/onglet/list.html.twig', [
            'professeurs' => $professeurs,
            'nomOnglet' => 'list',
            'routeName' => 'professeur_show',
            'context' => 'app_professeur'
        ]);
    }

    #[Route('/professeur/edt/show/{id}', name: 'show_true_edt', methods: ['POST', 'GET'])]
    public function showEdt(Professeur $professeur): Response
    {
        $edtProfesseur = $professeur->getEdtActuel();

        return $this->render('professeur/onglet/edt_show.html.twig', [
            'professeur' => $professeur,
            'edtProfesseur' => $edtProfesseur,
        ]);
    }
}
