<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ApiResource]
#[ORM\Entity]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateRealisation;

    #[ORM\ManyToMany(targetEntity: Exercice::class, inversedBy: 'seances')]
    #[ORM\JoinTable(name: 'Asso_5')]
    private Collection $exercices;

    public function __construct()
    {
        $this->exercices = new ArrayCollection();
    }

    // Getters et Setters
    public function getId(): int { return $this->id; }
    public function getDateRealisation(): \DateTimeInterface { return $this->dateRealisation; }
    public function setDateRealisation(\DateTimeInterface $date): self { $this->dateRealisation = $date; return $this; }
    public function getExercices(): Collection { return $this->exercices; }
    public function addExercice(Exercice $exercice): self { $this->exercices[] = $exercice; return $this; }
    public function removeExercice(Exercice $exercice): self { $this->exercices->removeElement($exercice); return $this; }
    public function getExercice(): ArrayCollection|Collection
    {
        return $this->exercices;
    }
    public function setExercice(ArrayCollection|Collection $exercices): self
    {
        $this->exercices = $exercices;
        return $this;
    }
    public function setExercices(ArrayCollection|Collection $exercices): self
    {
        $this->exercices = $exercices;
        return $this;
    }


}
