<?php

namespace App\Entity;

use App\Repository\FavoriActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriActiviteRepository::class)]
class FavoriActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getIdFavori(): ?int
    {
        return $this->idFavori;
    }

}
