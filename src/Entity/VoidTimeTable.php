<?php

namespace App\Entity;

use App\Repository\VoidTimeTableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoidTimeTableRepository::class)]
class VoidTimeTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?array $timetable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimetable(): ?array
    {
        return $this->timetable;
    }

    public function setTimetable(?array $timetable): static
    {
        $this->timetable = $timetable;

        return $this;
    }
}
