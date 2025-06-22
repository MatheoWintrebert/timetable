<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RoleController extends AbstractController
{
    #[Route('/role', name: 'app_role')]
    public function showRole(): Response
    {
        return $this->render('role/index.html.twig', [
            'controller_name' => 'RoleController',
        ]);
    }

    #[Route('/role/assign/{id}/{role}', name: 'app_role_assign')]
    public function assignRole(User $user, string $role, EntityManagerInterface $entityManager): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if (!str_starts_with($role, 'ROLE_')) {
            return new Response('Invalid role name', Response::HTTP_BAD_REQUEST);
        }

        $roles = $user->getRoles();
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_role', ['id' => $user->getId()]);
    }
}
