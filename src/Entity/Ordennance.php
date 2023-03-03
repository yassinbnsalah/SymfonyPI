<?php

namespace App\Entity;

use App\Repository\OrdennanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdennanceRepository::class)]
class Ordennance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"date is required")]
    private ?\DateTimeInterface $dateordenance = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"description is required")]
    #[Assert\GreaterThan(
        value: 0,
        message:"Amount must be possitive"
    )]
    private ?float $amount = null;

    #[ORM\OneToMany(mappedBy: 'ordennance', targetEntity: OrdennanceLigne::class)]
    private Collection $ordennanceLignes;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?RendezVous $RendezVous = null;

    public function __construct()
    {
        $this->ordennanceLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateordenance(): ?\DateTimeInterface
    {
        return $this->dateordenance;
    }

    public function setDateordenance(\DateTimeInterface $dateordenance): self
    {
        $this->dateordenance = $dateordenance;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection<int, OrdennanceLigne>
     */
    public function getOrdennanceLignes(): Collection
    {
        return $this->ordennanceLignes;
    }

    public function addOrdennanceLigne(OrdennanceLigne $ordennanceLigne): self
    {
        if (!$this->ordennanceLignes->contains($ordennanceLigne)) {
            $this->ordennanceLignes->add($ordennanceLigne);
            $ordennanceLigne->setOrdennance($this);
        }

        return $this;
    }

    public function removeOrdennanceLigne(OrdennanceLigne $ordennanceLigne): self
    {
        if ($this->ordennanceLignes->removeElement($ordennanceLigne)) {
            // set the owning side to null (unless already changed)
            if ($ordennanceLigne->getOrdennance() === $this) {
                $ordennanceLigne->setOrdennance(null);
            }
        }

        return $this;
    }

    public function getRendezVous(): ?RendezVous
    {
        return $this->RendezVous;
    }

    public function setRendezVous(?RendezVous $RendezVous): self
    {
        $this->RendezVous = $RendezVous;

        return $this;
    }
}
