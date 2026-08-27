<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['uid'], message: 'Cet UID est déjà utilisé.')]

class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::JSONB)]
    private array $roles = [];


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $promoMultipleCours = null;

    /**
     * @var Collection<int, ClassesRegistration>
     */
    #[ORM\OneToMany(targetEntity: ClassesRegistration::class, mappedBy: 'user', cascade: ['persist'])]
    private Collection $classesRegistrations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $priceToPay = null;

    #[ORM\Column(type: Types::JSONB, nullable: true)]
    private array $classesRegistrationID = [];

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->classesRegistrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?Uuid
    {
        return $this->uid;
    }

    public function setUid(Uuid $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeImmutable $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPromoMultipleCours(): ?string
    {
        return $this->promoMultipleCours;
    }

    public function setPromoMultipleCours(?string $promoMultipleCours): static
    {
        $this->promoMultipleCours = $promoMultipleCours;

        return $this;
    }

    /**
     * @return Collection<int, ClassesRegistration>
     */
    public function getClassesRegistrations(): Collection
    {
        return $this->classesRegistrations;
    }

    public function addClassesRegistration(ClassesRegistration $classesRegistration): static
    {
        if (!$this->classesRegistrations->contains($classesRegistration)) {
            $this->classesRegistrations->add($classesRegistration);
            $classesRegistration->setUser($this);
        }

        return $this;
    }

    public function removeClassesRegistration(ClassesRegistration $classesRegistration): static
    {
        if ($this->classesRegistrations->removeElement($classesRegistration)) {
            if ($classesRegistration->getUser() === $this) {
                $classesRegistration->setUser(null);
            }
        }

        return $this;
    }

    public function getPriceToPay(): ?string
    {
        return $this->priceToPay;
    }

    public function setPriceToPay(?string $priceToPay): static
    {
        $this->priceToPay = $priceToPay;

        return $this;
    }

    public function getClassesRegistrationID(): ?array
    {
        return $this->classesRegistrationID;
    }

    public function setClassesRegistrationID(array $classesRegistrationID): static
    {
        $this->classesRegistrationID = $classesRegistrationID;

        return $this;
    }
}
