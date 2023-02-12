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
    #[Groups("post:read")]
    private ?int $id = null;

    
    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups("post:read")]
    
    private ?int $CIN = null;


 
     #[ORM\Column(length:50)]
     #[Assert\Length(min : 2,max : 10,minMessage : "Votre Nom doit être au moins {{ limit }} characters long",maxMessage : "Votre Nom ne peut pas dépasser {{ limit }} characters")]
     #[Groups("post:read")]
    
    private ?string $Name= null;

    
     #[ORM\Column]
     #[Assert\NotBlank]
     #[Assert\Positive]
     #[Assert\Length(min :4,max : 8,minMessage :"Votre Numero doit être au moins {{ limit }} characters long",maxMessage : "Votre Numero ne peut pas dépasser {{ limit }} characters")]
     #[Groups("post:read")]
     private ?int $Numero  = null;

     #[ORM\Column]
     #[Assert\NotBlank]
     private ?int $Age  = null;

    #[ORM\Column(length:255)]
    #[Assert\Email(message : "The email '{{ value }}' is not a valid email.",  )]
    #[Groups("post:read")]
    
    private ?string $Email = null;

    
    #[ORM\Column(length:255)]
    #[Groups("post:read")]
   
    private ?string $Adresse = null;
    
    
      /**
     * @var string The hashed password
     */
     #[ORM\Column]
     #[Assert\Length(min : "8", minMessage:"Votre mot de passe doit faire minimum 8 carractéres")]
     
     private ?string $Password = null;

    
     #[Assert\EqualTo(propertyPath:"Password", message:"Vous n'avez pas tapé le méme mot de passe")]
     #[Groups("post:read")]
     
    public $confirm_password;

    
    #[ORM\Column(type:"json", nullable:true)]
    #[Groups("post:read")]
    
    private $roles = [];

   
     #[ORM\Column(type:"string", length:50, nullable:true)]
     #[Groups("post:read")]
     
    private $activation_token;

   
     #[ORM\Column(type:"string", length:50, nullable:true)]
     #[Groups("post:read")]
     
    private $reset_token;

    
     #[ORM\Column(type:"string", length:255, nullable:false)]
     #[Groups("post:read")]
     
    private $Image;

     #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subscription::class)]
     
     private Collection $subscriptions;

     #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Disponibility::class)]
     private Collection $disponibilities;

     public function __construct()
     {
         $this->subscriptions = new ArrayCollection();
         $this->disponibilities = new ArrayCollection();
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

}