<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Programme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Niveau::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Niveau $niveau;

    #[ORM\ManyToOne(targetEntity: Matiere::class, inversedBy: 'programmes')]
    #[ORM\JoinColumn(nullable: false)]
    private Matiere $matiere;

    #[ORM\Column(type: 'integer')]
    private int $nbHeures;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveau(): Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(Niveau $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }

    public function getMatiere(): Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(Matiere $matiere): self
    {
        $this->matiere = $matiere;
        return $this;
    }

    public function getNbHeures(): int
    {
        return $this->nbHeures;
    }

    public function setNbHeures(int $nbHeures): self
    {
        $this->nbHeures = $nbHeures;
        return $this;
    }
}
