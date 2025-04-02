<?php

namespace App\Entity;

use App\Repository\ExemplenameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExemplenameRepository::class)]
class Exemplename
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $propertynametest = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropertynametest(): ?string
    {
        return $this->propertynametest;
    }

    public function setPropertynametest(string $propertynametest): static
    {
        $this->propertynametest = $propertynametest;

        return $this;
    }
}
