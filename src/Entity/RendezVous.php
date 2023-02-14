<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateRV = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $HourRV = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DatePassageRV = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $HourPassageRV = null;

    #[ORM\Column(length: 255)]
    private ?string $State = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRV(): ?\DateTimeInterface
    {
        return $this->DateRV;
    }

    public function setDateRV(\DateTimeInterface $DateRV): self
    {
        $this->DateRV = $DateRV;

        return $this;
    }

    public function getHourRV(): ?\DateTimeInterface
    {
        return $this->HourRV;
    }

    public function setHourRV(\DateTimeInterface $HourRV): self
    {
        $this->HourRV = $HourRV;

        return $this;
    }

    public function getDatePassageRV(): ?\DateTimeInterface
    {
        return $this->DatePassageRV;
    }

    public function setDatePassageRV(\DateTimeInterface $DatePassageRV): self
    {
        $this->DatePassageRV = $DatePassageRV;

        return $this;
    }

    public function getHourPassageRV(): ?\DateTimeInterface
    {
        return $this->HourPassageRV;
    }

    public function setHourPassageRV(\DateTimeInterface $HourPassageRV): self
    {
        $this->HourPassageRV = $HourPassageRV;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->State;
    }

    public function setState(string $State): self
    {
        $this->State = $State;

        return $this;
    }
}
