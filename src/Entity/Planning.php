<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanningRepository::class)]
class Planning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 125)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $dietNote = null;

    #[ORM\ManyToOne(inversedBy: 'plannings')]
    private ?Seance $seance = null;

    #[ORM\ManyToOne(inversedBy: 'plannings')]
    private ?Activity $activity = null;

    #[ORM\ManyToOne(inversedBy: 'plannings')]
    private ?User $coach = null;

    #[ORM\Column(length: 15)]
    private ?string $salle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePlanning = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDietNote(): ?string
    {
        return $this->dietNote;
    }

    public function setDietNote(string $dietNote): self
    {
        $this->dietNote = $dietNote;

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getCoach(): ?User
    {
        return $this->coach;
    }

    public function setCoach(?User $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(string $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getDatePlanning(): ?\DateTimeInterface
    {
        return $this->datePlanning;
    }

    public function setDatePlanning(\DateTimeInterface $datePlanning): self
    {
        $this->datePlanning = $datePlanning;

        return $this;
    }
}
