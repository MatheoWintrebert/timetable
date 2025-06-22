<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Form\NiveauType;
use App\Entity\Niveau;

class NiveauController extends AbstractController
{
    private OngletService $ongletService;
    public function __construct(OngletService $ongletService)
    {
        $this->ongletService = $ongletService;
    }

    // Route principale pour afficher l'index
    #[Route(path: '/niveau', name: 'app_niveau')]
    public function index(): Response
    {
        return $this->render(view: 'niveau/index.html.twig');
    }

    // Route dynamique pour afficher l'onglet (nouveau ou liste)
    #[Route('/niveau/onglet/{nomOnglet}', name: 'niveau_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        // Enregistrer l'onglet actif dans la session
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);

        // Définir les variables de base
        $routeName = 'niveau_show';
        $context = 'app_niveau';
        $niveau = new Niveau();
        $form = null;

        // Si l'onglet est "new", afficher le formulaire
        if ($nomOnglet === "new") {
            $form = $this->createForm(NiveauType::class, $niveau);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($niveau);
                $entityManager->flush();

                // Optionnel : Réinitialiser le form après succès
                $niveau = new Niveau();
                $form = $this->createForm(NiveauType::class, $niveau);

                $this->addFlash('success', 'Niveau créée avec succès !');
            }

            $data['form'] = $form->createView();

            return $this->render('niveau/onglet/new.html.twig', $data);
        }


        // Définir les données à passer à la vue
        $data = [
            'routeName' => $routeName,
            'nomOnglet' => $nomOnglet,
            'context' => $context,
        ];

        // Si l'onglet est "list", afficher la liste des niveaux
        if ($nomOnglet === "list") {
            $data['niveaux'] = $entityManager->getRepository(Niveau::class)->findAll();
        }

        // Si l'onglet est inconnu, rediriger ou afficher une erreur
        return $this->render(
            view: 'niveau/onglet/' . $nomOnglet . '.html.twig',
            parameters:
            $data
        );
    }

    #[Route('/niveau/delete/{id}', name: 'niveau_delete', methods: ['POST'])]
    public function delete(Request $request, Niveau $niveau, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_niveau_' . $niveau->getId(), $request->request->get('_token'))) {
            $em->remove($niveau);
            $em->flush();
        }

        // Re-render partiel (liste des niveaux)
        $niveaux = $em->getRepository(Niveau::class)->findAll();
        return $this->render('niveau/onglet/list.html.twig', [
            'niveaux' => $niveaux,
            'nomOnglet' => 'list',
            'routeName' => 'niveau_show',
            'context' => 'app_niveau'
        ]);
    }
}
