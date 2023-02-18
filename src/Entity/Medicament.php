<?php

namespace App\Entity;

use App\Repository\MedicamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicamentRepository::class)]
class Medicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\OneToMany(mappedBy: 'medicament', targetEntity: OrdennanceLigne::class)]
    private Collection $ordennanceLignes;

    public function __construct()
    {
        $this->ordennanceLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

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
            $ordennanceLigne->setMedicament($this);
        }

        return $this;
    }

    public function removeOrdennanceLigne(OrdennanceLigne $ordennanceLigne): self
    {
        if ($this->ordennanceLignes->removeElement($ordennanceLigne)) {
            // set the owning side to null (unless already changed)
            if ($ordennanceLigne->getMedicament() === $this) {
                $ordennanceLigne->setMedicament(null);
            }
        }

        return $this;
    }
}
