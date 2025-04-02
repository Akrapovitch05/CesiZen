<?php

namespace App\Entity;

use App\Repository\DiagnosticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticRepository::class)]
class Diagnostic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $scoreStress = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateRealisation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScoreStress(): ?int
    {
        return $this->scoreStress;
    }

    public function setScoreStress(int $scoreStress): self
    {
        $this->scoreStress = $scoreStress;
        return $this;
    }

    public function getDateRealisation(): ?\DateTimeInterface
    {
        return $this->dateRealisation;
    }

    public function setDateRealisation(\DateTimeInterface $dateRealisation): self
    {
        $this->dateRealisation = $dateRealisation;
        return $this;
    }
}
