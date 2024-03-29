<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Date is required")]
    #[Assert\GreaterThanOrEqual("today",
    message:"Date must be higher than today")]  
    private ?\DateTimeInterface $DateRV = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $HourRV = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DatePassageRV = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $HourPassageRV = null;

    #[ORM\Column(length: 255)]
    private ?string $State = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    private ?User $fromuser = null;

    #[ORM\ManyToOne(inversedBy: 'rdvdoctor')]
    private ?User $todoctor = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Ordennance $ordenance = null;

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

    public function getFromuser(): ?User
    {
        return $this->fromuser;
    }

    public function setFromuser(?User $fromuser): self
    {
        $this->fromuser = $fromuser;

        return $this;
    }

    public function getTodoctor(): ?User
    {
        return $this->todoctor;
    }

    public function setTodoctor(?User $todoctor): self
    {
        $this->todoctor = $todoctor;

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

    public function getOrdennance(): ?Ordennance
    {
        return $this->ordenance;
    }
    public function setOrdennance(Ordennance $ordenance): self
    {
        $this->ordenance = $ordenance;

        return $this;
    }
   
}
