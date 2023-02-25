<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Assert\NotBlank(message:"Shipping Adress is required")]
    private ?string $shippingadress = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOrder = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $client = null;

    #[ORM\OneToMany(mappedBy: 'relatedOrder', targetEntity: OrderLine::class)]
    private Collection $orderLines;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $paiementmethod = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Invoiced = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $stripe_token = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $brand_stripe = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $last4_stripe = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $id_charge_stripe = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $status_stripe = null;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getShippingadress(): ?string
    {
        return $this->shippingadress;
    }

    public function setShippingadress(?string $shippingadress): self
    {
        $this->shippingadress = $shippingadress;

        return $this;
    }

    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->dateOrder;
    }

    public function setDateOrder(?\DateTimeInterface $dateOrder): self
    {
        $this->dateOrder = $dateOrder;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setRelatedOrder($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getRelatedOrder() === $this) {
                $orderLine->setRelatedOrder(null);
            }
        }

        return $this;
    }

    public function getPaiementmethod(): ?string
    {
        return $this->paiementmethod;
    }

    public function setPaiementmethod(?string $paiementmethod): self
    {
        $this->paiementmethod = $paiementmethod;

        return $this;
    }

    public function isInvoiced(): ?bool
    {
        return $this->Invoiced;
    }

    public function setInvoiced(?bool $Invoiced): self
    {
        $this->Invoiced = $Invoiced;

        return $this;
    }

    // public function getStripeToken(): ?string
    // {
    //     return $this->stripe_token;
    // }

    // public function setStripeToken(?string $stripe_token): self
    // {
    //     $this->stripe_token = $stripe_token;

    //     return $this;
    // }

    // public function getBrandStripe(): ?string
    // {
    //     return $this->brand_stripe;
    // }

    // public function setBrandStripe(?string $brand_stripe): self
    // {
    //     $this->brand_stripe = $brand_stripe;

    //     return $this;
    // }

    // public function getLast4Stripe(): ?string
    // {
    //     return $this->last4_stripe;
    // }

    // public function setLast4Stripe(?string $last4_stripe): self
    // {
    //     $this->last4_stripe = $last4_stripe;

    //     return $this;
    // }

    // public function getIdChargeStripe(): ?string
    // {
    //     return $this->id_charge_stripe;
    // }

    // public function setIdChargeStripe(?string $id_charge_stripe): self
    // {
    //     $this->id_charge_stripe = $id_charge_stripe;

    //     return $this;
    // }

    // public function getStatusStripe(): ?string
    // {
    //     return $this->status_stripe;
    // }

    // public function setStatusStripe(?string $status_stripe): self
    // {
    //     $this->status_stripe = $status_stripe;

    //     return $this;
    // }
}
