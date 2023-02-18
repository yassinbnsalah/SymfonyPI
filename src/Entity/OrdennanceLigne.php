<?php

namespace App\Entity;

use App\Repository\OrdennanceLigneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdennanceLigneRepository::class)]
class OrdennanceLigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $qunatite = null;

    #[ORM\ManyToOne(inversedBy: 'ordennanceLignes')]
    private ?Medicament $medicament = null;

    #[ORM\ManyToOne(inversedBy: 'ordennanceLignes')]
    private ?Ordennance $ordennance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQunatite(): ?int
    {
        return $this->qunatite;
    }

    public function setQunatite(int $qunatite): self
    {
        $this->qunatite = $qunatite;

        return $this;
    }

    public function getMedicament(): ?Medicament
    {
        return $this->medicament;
    }

    public function setMedicament(?Medicament $medicament): self
    {
        $this->medicament = $medicament;

        return $this;
    }

    public function getOrdennance(): ?Ordennance
    {
        return $this->ordennance;
    }

    public function setOrdennance(?Ordennance $ordennance): self
    {
        $this->ordennance = $ordennance;

        return $this;
    }
}
