<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OngletService;
use App\Form\UpdatePasswordType;

final class AccountManagerController extends AbstractController
{
    private OngletService $ongletService;
    public function __construct(OngletService $ongletService)
    {
        $this->ongletService = $ongletService;
    }

    #[Route(path: '/account-manager', name: 'app_account_manager')]
    public function index(): Response
    {
        return $this->render(view: 'account_manager/index.html.twig');
    }

    #[Route('/account_manager/onglet/{nomOnglet}', name: 'account_manager_show')]
    public function show(Request $request, string $nomOnglet, EntityManagerInterface $entityManager): Response
    {
        $this->ongletService->setActiveOnglet(onglet: $nomOnglet);

        // Définir les variables de base
        $routeName = 'account_manager_show';
        $context = 'app_account_manager';
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Liste des rôles et leur nom lisible
        $roleLabels = [
            'ROLE_ADMIN' => 'Admin',
            'ROLE_USER' => 'User',
            'ROLE_PROF' => 'Prof',
        ];

        // Récupérer les rôles de l'utilisateur
        $userRoles = $user->getRoles();

        // Transformer les rôles en noms lisibles
        $displayRoles = [];
        foreach ($userRoles as $role) {
            if (isset($roleLabels[$role])) {
                $displayRoles[] = $roleLabels[$role];
            } else {
                $displayRoles[] = $role; // au cas où un rôle n'est pas dans la liste
            }
        }
        if (in_array('ROLE_PROF', $userRoles)) {
            $professeurId = $user->getProfesseur()->getId();
        } else {
            $professeurId = 99;
        }
        return $this->render('account_manager/onglet/affichage.html.twig', [
            'controller_name' => 'AccountManagerController',
            'display_roles' => $displayRoles,
            'professeurId' => $professeurId,
        ]);
    }


    #[Route('/account-manager/new-password', name: 'app_account_manager_password_update')]
    public function updatePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UpdatePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();


            if (!$userPasswordHasher->hashPassword($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe est incorrect.');
                return $this->redirectToRoute('app_account_manager');
            }

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_account_manager');
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour !');

            return $this->redirectToRoute('app_main');

        }


        return $this->render('account_manager/update_password.html.twig', [
            'controller_name' => 'AccountManagerController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account-manager/update-email', name: 'app_account_manager_update_email')]
    public function updateEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(EmailType::class);
    }
}
