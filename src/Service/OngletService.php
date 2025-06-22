<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class OngletService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    // Récupérer l'onglet actif dans la session
    public function getActiveOnglet(): ?string
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        return $session->get('onglet_active', 'onglet1');
    }

    // Enregistrer l'onglet actif dans la session
    public function setActiveOnglet(string $onglet): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set('onglet_active', $onglet);
    }
}