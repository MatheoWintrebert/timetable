<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Professeur;

#[ORM\Entity]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\OneToMany(targetEntity: Programme::class, mappedBy: 'matiere')]
    private Collection $programmes;

    #[ORM\ManyToMany(targetEntity: Professeur::class, mappedBy: 'matieres')]
    private Collection $professeurs;

    #[ORM\Column(type: 'string', length: 7)]
    private ?string $couleur = null;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
        $this->professeurs = new ArrayCollection();
    }

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

    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function getProfesseurs(): Collection
    {
        return $this->professeurs;
    }

    // Ajout d'un professeur à une matière
    public function addProfesseur(Professeur $professeur): self
    {
        if (!$this->professeurs->contains($professeur)) {
            $this->professeurs[] = $professeur;
            $professeur->addMatiere($this);  // Ajouter la matière au professeur (relation bidirectionnelle)
        }
        return $this;
    }

    // Suppression d'un professeur d'une matière
    public function removeProfesseur(Professeur $professeur): self
    {
        if ($this->professeurs->contains($professeur)) {
            $this->professeurs->removeElement($professeur);
            $professeur->removeMatiere($this);  // Supprimer la matière du professeur
        }
        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self
    {
        $this->couleur = $couleur;
        return $this;
    }
}
