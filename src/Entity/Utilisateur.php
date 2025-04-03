<?php
// src/Entity/Utilisateur.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 50)]
    private string $prenom;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateInscription;

    #[ORM\Column(type: 'string', length: 50)]
    private string $roles;

    #[ORM\ManyToOne(targetEntity: Seance::class)]
    #[ORM\JoinColumn(name: 'id_seance', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Seance $seance = null;

    #[ORM\ManyToMany(targetEntity: Activite::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinTable(name: 'Asso_7')]
    private Collection $activites;



    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->dateInscription = new \DateTime();
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function getUsername(): string
    {
        return $this->email;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles[0];
        return $this;
    }
    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;
        return $this;
    }
    public function getNom(): string
    {
        return $this->nom;
    }
    public function getPrenom(): string
    {
        return $this->prenom;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getDateInscription(): \DateTimeInterface
    {
        return $this->dateInscription;
    }
    public function getRoles(): array
    {
        return [$this->roles];
    }
    public function getSeance(): ?Seance
    {
        return $this->seance;
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
        if ($this->activites->contains($activite)) {
            $this->activites->removeElement($activite);
        }
        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
        return $this;
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return $this->email;
    }
}

