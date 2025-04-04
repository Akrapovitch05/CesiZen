<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ApiResource]
#[ORM\Entity]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $nom;

    #[ORM\Column(type: 'integer')]
    private int $dureeInspiration;

    #[ORM\Column(type: 'integer')]
    private int $dureeApnee;

    #[ORM\Column(type: 'integer')]
    private int $dureeExpiration;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\ManyToMany(targetEntity: Seance::class, mappedBy: 'exercices')]
    private Collection $seances;

    #[ORM\ManyToMany(targetEntity: Activite::class, mappedBy: 'exercices')]
    private Collection $activites;
    

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->activites = new ArrayCollection();
    }

    // Getters et Setters
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getDureeInspiration(): int { return $this->dureeInspiration; }
    public function setDureeInspiration(int $duree): self { $this->dureeInspiration = $duree; return $this; }
    public function getDureeApnee(): int { return $this->dureeApnee; }
    public function setDureeApnee(int $duree): self { $this->dureeApnee = $duree; return $this; }
    public function getDureeExpiration(): int { return $this->dureeExpiration; }
    public function setDureeExpiration(int $duree): self { $this->dureeExpiration = $duree; return $this; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getSeances(): Collection { return $this->seances; }
    public function addSeance(Seance $seance): self { $this->seances[] = $seance; return $this; }
    public function removeSeance(Seance $seance): self { $this->seances->removeElement($seance); return $this; }
    public function getActivites(): Collection { return $this->activites; }
    public function addActivite(Activite $activite): self { $this->activites[] = $activite; return $this; }
    public function removeActivite(Activite $activite): self { $this->activites->removeElement($activite); return $this; }
}