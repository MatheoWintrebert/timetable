<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Professeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private string $prenom;

    #[ORM\Column(name: 'nb_heures_max', type: 'integer')]
    private int $nbHeuresMax;

    #[ORM\Column(type: 'json', nullable: false)]
    private array $preferenceHoraire = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $edtActuel = [];

    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'professeurs')]
    #[ORM\JoinTable(name: 'professeur_matiere')]
    private Collection $matieres;

    public function __construct()
    {
        $this->matieres = new ArrayCollection();
        $this->preferenceHoraire = [];
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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNbHeuresMax(): int
    {
        return $this->nbHeuresMax;
    }

    public function setNbHeuresMax(int $nbHeuresMax): self
    {
        $this->nbHeuresMax = $nbHeuresMax;
        return $this;
    }

    public function getPreferenceHoraire(): ?array
    {
        return $this->preferenceHoraire;
    }

    public function setPreferenceHoraire(?array $preferenceHoraire): self
    {
        $this->preferenceHoraire = $preferenceHoraire;
        return $this;
    }

    public function getEdtActuel(): ?array
    {
        return $this->edtActuel;
    }

    public function setEdtActuel(?array $edtActuel): self
    {
        $this->edtActuel = $edtActuel;
        return $this;
    }

    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matiere $matiere): self
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres[] = $matiere;
        }
        return $this;
    }

    public function removeMatiere(Matiere $matiere): self
    {
        $this->matieres->removeElement($matiere);
        return $this;
    }
}
