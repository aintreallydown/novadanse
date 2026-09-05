<?php

namespace App\Entity;

use App\Repository\ClassesRegistrationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ClassesRegistrationRepository::class)]
class ClassesRegistration
{
    public const COURS = [

        'ados-lundi' => [
            'label' => 'Ados · 11–15 ans · Lundi (17h30-19h)',
            'prix' => 225,
        ],
        'adulte1-lundi' => [
            'label' => 'Adulte I · +15 ans · Lundi (19h-20h30)',
            'prix' => 225,
        ],
        'eveil1-mercredi' => [
            'label' => 'Éveil I · 4–5 ans · Mercredi (17h-17h45)',
            'prix' => 180,
        ],
        'eveil2-mercredi' => [
            'label' => 'Éveil II · 5–6 ans · Mercredi (18h-18h45)',
            'prix' => 180,
        ],
        'initiation-mercredi' => [
            'label' => 'Initiation · 7–10 ans · Mercredi (18h45-19h45)',
            'prix' => 195,
        ],
        'bien-etre-mercredi' => [
            'label' => 'Danse et bien-être · +15 ans · Mercredi (19h45-21h)',
            'prix' => 210,
        ],
        'adulte2-samedi' => [
            'label' => 'Adulte II · +15 ans · Samedi (10h30-12h)',
            'prix' => 225,
        ],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'classesRegistrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::JSONB)]
    private array $produit = [];

    #[ORM\Column(length: 255)]
    private ?string $contactUrgence = null;

    #[ORM\Column(length: 255)]
    private ?string $telephoneContactUrgence = null;

    #[ORM\Column]
    private ?bool $needMedicalCertificate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $medicalCertificateFile = null;

    #[ORM\Column]
    private ?bool $ancienAdherent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passPortCode = null;

    #[ORM\Column(nullable: true)]
    private ?bool $autorisationParentale = null;

    #[ORM\Column]
    private ?bool $droitImage = null;

    #[ORM\Column]
    private ?bool $cgv = null;

    #[ORM\Column]
    private ?bool $reglementInterieur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $PromoEstresSaintDenis = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?Uuid $uid = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeImmutable $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getProduit(): array
    {
        return $this->produit;
    }

    public function setProduit(array $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getContactUrgence(): ?string
    {
        return $this->contactUrgence;
    }

    public function setContactUrgence(string $contactUrgence): static
    {
        $this->contactUrgence = $contactUrgence;

        return $this;
    }

    public function getTelephoneContactUrgence(): ?string
    {
        return $this->telephoneContactUrgence;
    }

    public function setTelephoneContactUrgence(string $telephoneContactUrgence): static
    {
        $this->telephoneContactUrgence = $telephoneContactUrgence;

        return $this;
    }

    public function isNeedMedicalCertificate(): ?bool
    {
        return $this->needMedicalCertificate;
    }

    public function setNeedMedicalCertificate(bool $needMedicalCertificate): static
    {
        $this->needMedicalCertificate = $needMedicalCertificate;

        return $this;
    }

    public function getMedicalCertificateFile(): ?string
    {
        return $this->medicalCertificateFile;
    }

    public function setMedicalCertificateFile(?string $medicalCertificateFile): static
    {
        $this->medicalCertificateFile = $medicalCertificateFile;

        return $this;
    }

    public function isAncienAdherent(): ?bool
    {
        return $this->ancienAdherent;
    }

    public function setAncienAdherent(bool $ancienAdherent): static
    {
        $this->ancienAdherent = $ancienAdherent;

        return $this;
    }

    public function getPassPortCode(): ?string
    {
        return $this->passPortCode;
    }

    public function setPassPortCode(?string $passPortCode): static
    {
        $this->passPortCode = $passPortCode;

        return $this;
    }

    public function isAutorisationParentale(): ?bool
    {
        return $this->autorisationParentale;
    }

    public function setAutorisationParentale(?bool $autorisationParentale): static
    {
        $this->autorisationParentale = $autorisationParentale;

        return $this;
    }

    public function isDroitImage(): ?bool
    {
        return $this->droitImage;
    }

    public function setDroitImage(bool $droitImage): static
    {
        $this->droitImage = $droitImage;

        return $this;
    }

    public function isCgv(): ?bool
    {
        return $this->cgv;
    }

    public function setCgv(bool $cgv): static
    {
        $this->cgv = $cgv;

        return $this;
    }

    public function isReglementInterieur(): ?bool
    {
        return $this->reglementInterieur;
    }

    public function setReglementInterieur(bool $reglementInterieur): static
    {
        $this->reglementInterieur = $reglementInterieur;

        return $this;
    }

    /**
     * Retourne les choix formatés pour le ChoiceType du formulaire.
     */
    public static function getCoursChoices(): array
    {
        $choices = [];
        foreach (self::COURS as $slug => $data) {
            $choices["{$data['label']} - {$data['prix']}€"] = $slug;
        }
        return $choices;
    }

    /**
     * Retourne le prix d'un cours donné (par son slug).
     */
    public static function getPrixCours(string $slug): ?int
    {
        return self::COURS[$slug]['prix'] ?? null;
    }

    /**
     * Calcule le total à payer pour les cours sélectionnés sur ce dossier.
     */
    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->produit as $slug) {
            $total += self::getPrixCours($slug) ?? 0;
        }
        return $total;
    }

    /**
     * Retourne les libellés lisibles des cours choisis (pour affichage récap/Twig).
     */
    public function getProduitLabels(): array
    {
        return array_map(
            fn(string $slug) => self::COURS[$slug]['label'] ?? $slug,
            $this->produit
        );
    }

    public function isPromoEstresSaintDenis(): ?bool
    {
        return $this->PromoEstresSaintDenis;
    }

    public function setPromoEstresSaintDenis(?bool $PromoEstresSaintDenis): static
    {
        $this->PromoEstresSaintDenis = $PromoEstresSaintDenis;

        return $this;
    }

    public function getUid(): ?Uuid
    {
        return $this->uid;
    }

    public function setUid(?Uuid $uid): static
    {
        $this->uid = $uid;

        return $this;
    }
}
