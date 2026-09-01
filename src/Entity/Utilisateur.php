<?php
// src/Entity/Utilisateur.php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Compte utilisateur de la plateforme.
 *
 * L'application traitant des donnees de sante mentale, cette entite est
 * consideree comme sensible au sens du RGPD : son exposition via l'API est
 * restreinte, et le mot de passe n'est jamais serialise.
 */
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[UniqueEntity(
    fields: ['email'],
    message: 'Un compte existe deja avec cette adresse electronique.'
)]
// Exposition API volontairement restreinte : lecture seule, reservee aux
// administrateurs. Sans cette restriction, /api/utilisateurs listait
// l'integralite des comptes a un visiteur anonyme.
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['utilisateur:lecture']],
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['utilisateur:lecture'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le nom ne peut pas depasser {{ limit }} caracteres.')]
    #[Groups(['utilisateur:lecture'])]
    private string $nom = '';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Le prenom est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le prenom ne peut pas depasser {{ limit }} caracteres.')]
    #[Groups(['utilisateur:lecture'])]
    private string $prenom = '';

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'adresse electronique est obligatoire.')]
    #[Assert\Email(message: 'Cette adresse electronique n\'est pas valide.')]
    #[Assert\Length(max: 180)]
    #[Groups(['utilisateur:lecture'])]
    private string $email = '';

    /**
     * Mot de passe hache. Jamais expose dans aucun groupe de serialisation :
     * une fuite de hachages permettrait une attaque par dictionnaire hors ligne.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $password = '';

    /**
     * Mot de passe en clair, uniquement le temps du formulaire.
     * Non persiste, et efface par eraseCredentials() apres authentification.
     */
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.', groups: ['inscription'])]
    #[Assert\Length(
        min: 12,
        max: 4096,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caracteres.',
        groups: ['inscription']
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/',
        message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractere special.',
        groups: ['inscription']
    )]
    #[Assert\NotCompromisedPassword(
        message: 'Ce mot de passe figure dans une fuite de donnees connue. Choisissez-en un autre.',
        groups: ['inscription']
    )]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'date')]
    #[Groups(['utilisateur:lecture'])]
    private \DateTimeInterface $dateInscription;

    /**
     * Roles applicatifs, stockes en JSON.
     *
     * Le champ etait auparavant une chaine de 50 caracteres ne pouvant porter
     * qu'un seul role, et getRoles() pouvait renvoyer un tableau contenant
     * null pour un compte sans role — ce que Symfony interprete mal.
     *
     * @var list<string>
     */
    #[ORM\Column(type: 'json')]
    #[Groups(['utilisateur:lecture'])]
    private array $roles = [];

    #[ORM\ManyToOne(targetEntity: Seance::class)]
    #[ORM\JoinColumn(name: 'id_seance', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Seance $seance = null;

    #[ORM\ManyToMany(targetEntity: Activite::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinTable(name: 'utilisateur_activite')]
    private Collection $activites;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->dateInscription = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getDateInscription(): \DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // ROLE_USER est garanti a tout compte authentifie : c'est la
        // convention Symfony, et elle evite qu'un compte sans role explicite
        // se retrouve sans aucune autorisation.
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;
        return $this;
    }

    public function getActivites(): Collection
    {
        return $this->activites;
    }

    public function addActivite(Activite $activite): self
    {
        if (!$this->activites->contains($activite)) {
            $this->activites[] = $activite;
        }
        return $this;
    }

    public function removeActivite(Activite $activite): self
    {
        $this->activites->removeElement($activite);
        return $this;
    }

    /**
     * Identifiant unique presente a Symfony pour l'authentification.
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Efface les donnees sensibles temporaires apres authentification, pour
     * qu'un mot de passe en clair ne subsiste ni en memoire ni en session.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
