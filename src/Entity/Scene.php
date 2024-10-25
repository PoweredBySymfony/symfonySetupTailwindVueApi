<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SceneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SceneRepository::class)]
#[ApiResource(
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete()
)]
class Scene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $nombreMaxParticipants = null;

    #[ORM\ManyToOne(inversedBy: 'scenes')]
    private ?EvenementMusical $evenementMusical = null;

    #[ORM\ManyToOne(inversedBy: 'scenes')]
    private ?PartieConcert $partieConcerts = null;

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

    public function getNombreMaxParticipants(): ?int
    {
        return $this->nombreMaxParticipants;
    }

    public function setNombreMaxParticipants(int $nombreMaxParticipants): static
    {
        $this->nombreMaxParticipants = $nombreMaxParticipants;

        return $this;
    }

    public function getEvenementMusical(): ?EvenementMusical
    {
        return $this->evenementMusical;
    }

    public function setEvenementMusical(?EvenementMusical $evenementMusical): static
    {
        $this->evenementMusical = $evenementMusical;

        return $this;
    }

    public function getPartieConcerts(): ?PartieConcert
    {
        return $this->partieConcerts;
    }

    public function setPartieConcerts(?PartieConcert $partieConcerts): static
    {
        $this->partieConcerts = $partieConcerts;

        return $this;
    }
}
