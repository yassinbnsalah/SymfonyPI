<?php

namespace App\Entity;

use App\Repository\DisponibilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DisponibilityRepository::class)]
class Disponibility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    
    #[Assert\NotBlank(message:"Date is required")]
    #[Assert\GreaterThanOrEqual("today",
    message:"Date must be higher than today")]     
    private ?\DateTimeInterface $dateDispo = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]

    private ?\DateTimeInterface $heureEnd = null;

    #[ORM\Column(length: 255)]
    private ?string $Note = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilities')]
    private ?User $doctor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDispo(): ?\DateTimeInterface
    {
        return $this->dateDispo;
    }

    public function setDateDispo(\DateTimeInterface $dateDispo): self
    {
        $this->dateDispo = $dateDispo;

        return $this;
    }

    public function getHeureStart(): ?\DateTimeInterface
    {
        return $this->heureStart;
    }

    public function setHeureStart(\DateTimeInterface $heureStart): self
    {
        $this->heureStart = $heureStart;

        return $this;
    }

    public function getHeureEnd(): ?\DateTimeInterface
    {
        return $this->heureEnd;
    }

    public function setHeureEnd(\DateTimeInterface $heureEnd): self
    {
        $this->heureEnd = $heureEnd;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->Note;
    }

    public function setNote(string $Note): self
    {
        $this->Note = $Note;

        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }
}
