<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: 'integer')]
    private ?int $dureeInspiration = null;

    #[ORM\Column(type: 'integer')]
    private ?int $dureeApnee = null;

    #[ORM\Column(type: 'integer')]
    private ?int $dureeExpiration = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDureeInspiration(): ?int
    {
        return $this->dureeInspiration;
    }

    public function setDureeInspiration(int $dureeInspiration): self
    {
        $this->dureeInspiration = $dureeInspiration;
        return $this;
    }

    public function getDureeApnee(): ?int
    {
        return $this->dureeApnee;
    }

    public function setDureeApnee(int $dureeApnee): self
    {
        $this->dureeApnee = $dureeApnee;
        return $this;
    }

    public function getDureeExpiration(): ?string
    {
        return $this->dureeExpiration;
    }

    public function setDureeExpiration(string $dureeExpiration): self
    {
        $this->dureeExpiration = $dureeExpiration;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
