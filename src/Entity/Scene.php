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
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SceneRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ["groups" => ["scene:create"]],
            normalizationContext: ["groups" => ["scene:read"]],
        ),
        new Patch(
            denormalizationContext: ["groups" => ["scene:update"]],
            normalizationContext: ["groups" => ["scene:read"]],
        ),
        new Delete()
    ],
    normalizationContext: ["groups" => ["scene:read"]]
)]
class Scene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["scene:read", "partie_concert:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["scene:read", "scene:create", "scene:update", "partie_concert:read"])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Groups(["scene:read", "scene:create", "scene:update", "partie_concert:read"])]
    private ?int $nombreMaxParticipants = null;

    #[ORM\ManyToOne(inversedBy: 'scenes')]
    #[Groups(["scene:read", "partie_concert:read", "scene:create"])]
    private ?EvenementMusical $evenementMusical = null;

    #[ORM\ManyToOne(inversedBy: 'scenes')]
    #[Groups(["scene:read", "scene:create"])]
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