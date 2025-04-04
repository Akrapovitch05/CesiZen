<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ApiResource]
#[ORM\Entity]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\Column(type: 'integer')]
    private int $duree;

    #[ORM\Column(type: 'string', length: 255)]
    private string $urlMedia;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'activites')]
    private Collection $utilisateurs;
    #[ORM\ManyToMany(targetEntity: Exercice::class, inversedBy: "activites")]
    private Collection $exercices;


    public function __construct()
    {
        $this->exercices = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->nom;
    }
    public function getNom(): string
    {
        return $this->nom;
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
    public function getExercice(): ArrayCollection|Collection
    {
        return $this->exercices;
    }
    public function setExercice(ArrayCollection|Collection $exercices): self
    {
        $this->exercices = $exercices;
        return $this;
    }


    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getUrlMedia(): string
    {
        return $this->urlMedia;
    }

    public function setUrlMedia(string $urlMedia): self
    {
        $this->urlMedia = $urlMedia;
        return $this;
    }

    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function addExercice(Exercice $exercice): self
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices[] = $exercice;
            $exercice->addActivite($this); // Assurer la réciproque de la relation
        }
        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        if ($this->exercices->contains($exercice)) {
            $this->exercices->removeElement($exercice);
            $exercice->removeActivite($this); // Assurer la réciproque de la relation
        }
        return $this;
    }
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }
    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
        }
        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
        }
        return $this;
    }
}
