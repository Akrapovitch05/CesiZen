<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface,\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\ManyToOne(targetEntity: TrackerEmotion::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?TrackerEmotion $tracker = null;

    #[ORM\ManyToOne(targetEntity: Seance::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seance $seance = null;

    #[ORM\ManyToOne(targetEntity: Diagnostic::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Diagnostic $diagnostic = null;

    #[ORM\Column(type: 'json')]
    private array $roles = []; // Modification : 'roles' est maintenant un tableau (array)

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTime $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    // Getter et Setter pour 'diagnostic' (relation avec Diagnostic)
    public function getDiagnostic(): ?Diagnostic
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(?Diagnostic $diagnostic): static
    {
        $this->diagnostic = $diagnostic;
        return $this;
    }

    // Getter et Setter pour 'seance' (relation avec Seance)
    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): static
    {
        $this->seance = $seance;
        return $this;
    }

    // Getter et Setter pour 'tracker' (relation avec TrackerEmotion)
    public function getTracker(): ?TrackerEmotion
    {
        return $this->tracker;
    }

    public function setTracker(?TrackerEmotion $tracker): static
    {
        $this->tracker = $tracker;
        return $this;
    }

    public function getRoles(): array
    {
        // Assure-toi que chaque utilisateur ait au moins le rôle ROLE_USER
        $roles = $this->roles;
        // Assurer que tous les utilisateurs aient au moins un rôle par défaut
        $roles[] = 'ROLE_USER';

        return array_unique($roles); // On retourne un tableau de rôles unique
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return $this->email;
    }
    public function __construct()
    {
        $this->dateInscription = new \DateTime(); // Date et heure actuelles lors de l'instanciation
    }

}
