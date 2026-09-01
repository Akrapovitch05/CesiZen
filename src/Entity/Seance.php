<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
// Une seance est rattachee a un utilisateur : jamais exposee anonymement.
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
)]
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
    #[ORM\JoinTable(name: 'seance_exercice')]
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
