<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/add-role/{id}/{role}', name: 'admin_add_role', methods: ['POST'])]
    public function addRole(User $user, string $role, EntityManagerInterface $em): RedirectResponse
    {
        $roles = $user->getRoles();
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $user->setRoles($roles);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/remove-role/{id}/{role}', name: 'admin_remove_role', methods: ['POST'])]
    public function removeRole(User $user, string $role, EntityManagerInterface $em): RedirectResponse
    {
        $roles = $user->getRoles();
        if (in_array($role, $roles)) {
            $roles = array_filter($roles, fn($r) => $r !== $role);
            $user->setRoles($roles);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}
