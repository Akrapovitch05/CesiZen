<?php

namespace App\Entity;

use App\Repository\EmotionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmotionRepository::class)]
class Emotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $niveau1 = null;

    #[ORM\Column(length: 50)]
    private ?string $niveau2 = null;

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

    public function getNiveau1(): ?string
    {
        return $this->niveau1;
    }

    public function setNiveau1(string $niveau1): static
    {
        $this->niveau1 = $niveau1;
        return $this;
    }

    public function getNiveau2(): ?string
    {
        return $this->niveau2;
    }

    public function setNiveau2(string $niveau2): static
    {
        $this->niveau2 = $niveau2;
        return $this;
    }

}
