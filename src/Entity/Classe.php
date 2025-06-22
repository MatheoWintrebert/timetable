<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $edtClasse = [];
    #[ORM\ManyToOne(targetEntity: Niveau::class, inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Niveau $niveau;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }
    public function getEdtClasse(): ?array
    {
        return $this->edtClasse;
    }

    public function setEdtClasse(?array $edtClasse): self
    {
        $this->edtClasse = $edtClasse;
        return $this;
    }
}
