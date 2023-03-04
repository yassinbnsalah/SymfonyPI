<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message:"Date is required")]
    #[Assert\GreaterThanOrEqual("today",
    message:"Date must be higher than today")]  
    #[Groups("subscribers")]
    // #[Assert\Date(message :"le date ne doit etre ")]
    private ?\DateTimeInterface $dateSub = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("subscribers")]
    private ?\DateTimeInterface $dateExpire = null;

    #[ORM\Column(length: 125)]
    #[Assert\NotBlank(message:"Type is required")]
    #[Groups("subscribers")]
    private ?string $type = null;

    #[ORM\Column(length: 125)]
    #[Assert\NotBlank(message:"Paiment method is required")]
    #[Groups("subscribers")]
    private ?string $paiementType = null;

    #[ORM\Column]
    #[Assert\GreaterThan(
        value: 0,
        message:"Amount must be possitive"
    )]
    #[Assert\NotBlank(message:"Amount is required")]
    private ?int $amount = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    
    private ?User $user = null;

    #[ORM\Column(length: 125)]
    #[Groups("subscribers")]
    private ?string $state = null;

    #[ORM\Column(length: 125, nullable: true)]
    #[Groups("subscribers")]
    private ?string $reference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSub(): ?\DateTimeInterface
    {
        return $this->dateSub;
    }

    public function setDateSub(\DateTimeInterface $dateSub): self
    {
        $this->dateSub = $dateSub;

        return $this;
    }

    public function getDateExpire(): ?\DateTimeInterface
    {
        return $this->dateExpire;
    }

    public function setDateExpire(\DateTimeInterface $dateExpire): self
    {
        $this->dateExpire = $dateExpire;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPaiementType(): ?string
    {
        return $this->paiementType;
    }

    public function setPaiementType(string $paiementType): self
    {
        $this->paiementType = $paiementType;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
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
}
