<?php

namespace App\Entity;

use App\Repository\DossierAdhesionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: DossierAdhesionRepository::class)]
class DossierAdhesion
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
            'label' => 'Initiation · 7–10 ans · Mercredi (18h-19h45)',
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

    #[ORM\OneToOne(mappedBy: 'dossierAdhesion', targetEntity: User::class)]
    private ?User $user = null;

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
    private ?bool $droitAImage = null;

    #[ORM\Column]
    private ?bool $cgv = null;

    #[ORM\Column]
    private ?bool $reglementInterieur = null;

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
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setDossierAdhesion(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getDossierAdhesion() !== $this) {
            $user->setDossierAdhesion($this);
        }

        $this->user = $user;

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

    public function isDroitAImage(): ?bool
    {
        return $this->droitAImage;
    }

    public function setDroitAImage(bool $droitAImage): static
    {
        $this->droitAImage = $droitAImage;

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
}
