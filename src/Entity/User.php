<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]

class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column]
    #[Assert\NotBlank(message:"Cin is required")]
    #[Assert\PositiveOrZero]
    private ?int $CIN = null;


 
     #[ORM\Column(length:50)]
     #[Assert\NotBlank(message:"Name is required")]
     #[Assert\Length(min : 4,max : 20,minMessage : "Votre Nom doit être au moins {{ limit }} characters long",maxMessage : "Votre Nom ne peut pas dépasser {{ limit }} characters")]

    
    private ?string $Name= null;

    
     #[ORM\Column]
     #[Assert\NotBlank(message:"Numero Telephone is required")]
     #[Assert\Positive]
     #[Assert\Length(min :4,max : 8,minMessage :"Votre Numero doit être au moins {{ limit }} characters long",maxMessage : "Votre Numero ne peut pas dépasser {{ limit }} characters")]

     private ?int $Numero  = null;

     #[ORM\Column]
     #[Assert\NotBlank(message:"Age is required")]
     #[Assert\Positive]
     private ?int $Age  = null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message : "The email '{{ value }}' is not a valid email.")]
   
    
    private ?string $Email = null;

    
    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Adresse is required")]

   
    private ?string $Adresse = null;
    
    
      /**
     * @var string The hashed password
     */
     #[ORM\Column]
     #[Assert\NotBlank]
     #[Assert\Length(min : "8", minMessage:"Votre mot de passe doit faire minimum 8 carractéres")]
     
     private ?string $Password = null;


    #[Assert\EqualTo(propertyPath:"Password", message:"Vous n'avez pas tapé le méme mot de passe")]
    #[Assert\NotBlank]
    public $confirm_password = null;

    
    #[ORM\Column(type:"json", nullable:true)]

    
    private $roles = [];

   
     #[ORM\Column(type:"string", length:50, nullable:true)]

     
    private $activation_token;

   
     #[ORM\Column(type:"string", length:50, nullable:true)]

     
    private $reset_token;

    
     #[ORM\Column(type:"string", length:255, nullable:true)]
  
     
    private $Image;

     #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subscription::class)]
     
     private Collection $subscriptions;

     #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Disponibility::class)]
     private Collection $disponibilities;

     #[ORM\OneToMany(mappedBy: 'fromuser', targetEntity: RendezVous::class)]
     private Collection $rendezVouses;

     #[ORM\OneToMany(mappedBy: 'todoctor', targetEntity: RendezVous::class)]
     private Collection $rdvdoctor;

     public function __construct()
     {
         $this->subscriptions = new ArrayCollection();
         $this->disponibilities = new ArrayCollection();
         $this->rendezVouses = new ArrayCollection();
         $this->rdvdoctor = new ArrayCollection();
     }

    public function __toString()
    {
        return (string) $this->CIN;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }
    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(int $Age): self
    {
        $this->Age = $Age;

        return $this;
    }
    public function getCIN(): ?int
    {
        return $this->CIN;
    }

    public function setCIN(int $CIN): self
    {
        $this->CIN = $CIN;

        return $this;
    }




    public function getNumero(): ?int
    {
        return $this->Numero;
    }

    public function setNumero(?int $Numero): self
    {
        $this->Numero = $Numero;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

     /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

  /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->Email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->Email;
    }

    public function eraseCredentials() {
        
    }
     public function getSalt() {
        
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRoles(string $roles): self
    {
        if (is_array($this->roles)) {
            if (!in_array($roles, $this->roles, true)) {
                $this->roles[] = $roles;
            }
        }

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setUser($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getUser() === $this) {
                $subscription->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Disponibility>
     */
    public function getDisponibilities(): Collection
    {
        return $this->disponibilities;
    }

    public function addDisponibility(Disponibility $disponibility): self
    {
        if (!$this->disponibilities->contains($disponibility)) {
            $this->disponibilities->add($disponibility);
            $disponibility->setDoctor($this);
        }

        return $this;
    }

    public function removeDisponibility(Disponibility $disponibility): self
    {
        if ($this->disponibilities->removeElement($disponibility)) {
            // set the owning side to null (unless already changed)
            if ($disponibility->getDoctor() === $this) {
                $disponibility->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): self
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setFromuser($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): self
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getFromuser() === $this) {
                $rendezVouse->setFromuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRdvdoctor(): Collection
    {
        return $this->rdvdoctor;
    }

    public function addRdvdoctor(RendezVous $rdvdoctor): self
    {
        if (!$this->rdvdoctor->contains($rdvdoctor)) {
            $this->rdvdoctor->add($rdvdoctor);
            $rdvdoctor->setTodoctor($this);
        }

        return $this;
    }

    public function removeRdvdoctor(RendezVous $rdvdoctor): self
    {
        if ($this->rdvdoctor->removeElement($rdvdoctor)) {
            // set the owning side to null (unless already changed)
            if ($rdvdoctor->getTodoctor() === $this) {
                $rdvdoctor->setTodoctor(null);
            }
        }

        return $this;
    }

}