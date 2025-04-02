<?php

namespace App\Entity;

use App\Repository\TrackerEmotionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrackerEmotionRepository::class)]
class TrackerEmotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEnregistrement = null;

    #[ORM\Column(type: 'integer')]
    private ?int $niveauEmotion = null;

    public function getIdTracker(): ?int
    {
        return $this->idTracker;
    }

    public function getDateEnregistrement(): ?\DateTimeInterface
    {
        return $this->dateEnregistrement;
    }

    public function setDateEnregistrement(\DateTimeInterface $dateEnregistrement): static
    {
        $this->dateEnregistrement = $dateEnregistrement;
        return $this;
    }

    public function getNiveauEmotion(): ?int
    {
        return $this->niveauEmotion;
    }

    public function setNiveauEmotion(int $niveauEmotion): static
    {
        $this->niveauEmotion = $niveauEmotion;
        return $this;
    }

}
