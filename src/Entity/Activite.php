<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    private ?int $duree = null;

    #[ORM\Column(length: 50)]
    private ?string $urlMedia = null;

    #[ORM\ManyToOne(targetEntity: FavoriActivite::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?FavoriActivite $favori = null;

    public function getIdActivite(): ?int
    {
        return $this->idActivite;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    public function getUrlMedia(): ?string
    {
        return $this->urlMedia;
    }

    public function setUrlMedia(string $urlMedia): static
    {
        $this->urlMedia = $urlMedia;
        return $this;
    }
    // Getter et Setter pour 'favori' (relation avec FavoriActivite)
    public function getFavori(): ?FavoriActivite
    {
        return $this->favori;
    }

    public function setFavori(?FavoriActivite $favori): static
    {
        $this->favori = $favori;
        return $this;
    }

}
