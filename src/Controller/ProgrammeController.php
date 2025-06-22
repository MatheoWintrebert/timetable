<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Form\ProgrammeType;
use App\Entity\Programme;

class ProgrammeController extends AbstractController
{
    private OngletService $ongletService;
    public function __construct(OngletService $ongletService)
    {
        $this->ongletService = $ongletService;
    }

    // Route principale pour afficher l'index
    #[Route(path: '/programme', name: 'app_programme')]
    public function index(): Response
    {
        return $this->render(view: 'programme/index.html.twig');
    }

    // Route dynamique pour afficher l'onglet (nouveau ou liste)
    #[Route('/programme/onglet/{nomOnglet}', name: 'programme_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        // Enregistrer l'onglet actif dans la session
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);

        // Définir les variables de base
        $routeName = 'programme_show';
        $context = 'app_programme';
        $programme = new Programme();
        $form = null;

        // Si l'onglet est "new", afficher le formulaire
        if ($nomOnglet === "new") {
            $form = $this->createForm(ProgrammeType::class, $programme);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($programme);
                $entityManager->flush();

                // Optionnel : Réinitialiser le form après succès
                $programme = new Programme();
                $form = $this->createForm(ProgrammeType::class, $programme);

                $this->addFlash('success', 'Programme créée avec succès !');
            }

            $data['form'] = $form->createView();

            return $this->render('programme/onglet/new.html.twig', $data);
        }


        // Définir les données à passer à la vue
        $data = [
            'routeName' => $routeName,
            'nomOnglet' => $nomOnglet,
            'context' => $context,
        ];

        // Si l'onglet est "list", afficher la liste des programmes
        if ($nomOnglet === "list") {
            $data['programmes'] = $entityManager->getRepository(Programme::class)->findAll();
        }

        // Si l'onglet est inconnu, rediriger ou afficher une erreur
        return $this->render(
            view: 'programme/onglet/' . $nomOnglet . '.html.twig',
            parameters:
            $data
        );
    }

    #[Route('/programme/delete/{id}', name: 'programme_delete', methods: ['POST'])]
    public function delete(Request $request, Programme $programme, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_programme_' . $programme->getId(), $request->request->get('_token'))) {
            $em->remove($programme);
            $em->flush();
        }

        // Re-render partiel (liste des programmes)
        $programmes = $em->getRepository(Programme::class)->findAll();
        return $this->render('programme/onglet/list.html.twig', [
            'programmes' => $programmes,
            'nomOnglet' => 'list',
            'routeName' => 'programme_show',
            'context' => 'app_programme'
        ]);
    }
}
