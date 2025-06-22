<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Form\MatiereType;
use App\Entity\Matiere;

class MatiereController extends AbstractController
{
    private OngletService $ongletService;
    public function __construct(OngletService $ongletService)
    {
        $this->ongletService = $ongletService;
    }

    #[Route(path: '/matiere', name: 'app_matiere')]
    public function index(): Response
    {
        return $this->render(view: 'matiere/index.html.twig');
    }
    
    #[Route('/matiere/onglet/{nomOnglet}', name: 'matiere_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);
        $matiere = new Matiere();
        $routeName = 'matiere_show';
        $context = 'app_matiere';
        $form = null;

        if ($nomOnglet === "new") {
            $form = $this->createForm(MatiereType::class, $matiere);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($matiere);
                $entityManager->flush();

                // Optionnel : Réinitialiser le form après succès
                $matiere = new Matiere();
                $form = $this->createForm(MatiereType::class, $matiere);

                $this->addFlash('success', 'Matière créée avec succès !');
            }

            $data['form'] = $form->createView();

            return $this->render('matiere/onglet/new.html.twig', $data);
        }
        $data = [
            'routeName' => $routeName,
            'nomOnglet' => $nomOnglet,
            'context' => $context,
        ];
        if ($nomOnglet === "list") {
            $data['matieres'] = $entityManager->getRepository(Matiere::class)->findAll();
        }

        return $this->render(
            view: 'matiere/onglet/' . $nomOnglet . '.html.twig',
            parameters:
            $data
        );
    }
    #[Route('/matiere/delete/{id}', name: 'matiere_delete', methods: ['POST'])]
    public function delete(Request $request, Matiere $matiere, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_matiere_' . $matiere->getId(), $request->request->get('_token'))) {
            $em->remove($matiere);
            $em->flush();
        }

        // Re-render partiel (liste des matières)
        $matieres = $em->getRepository(Matiere::class)->findAll();
        return $this->render('matiere/onglet/list.html.twig', [
            'matieres' => $matieres,
            'nomOnglet' => 'list',
            'routeName' => 'matiere_show',
            'context' => 'app_matiere'
        ]);
    }
}
