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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SceneRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: "is_granted('SCENE_EDIT', object) and object == user",
            validationContext: ['groups' => ['scene:create']]
        ),
        new Patch(
            security: "is_granted('SCENE_EDIT', object) and object == user",
            validationContext: ['groups' => ['scene:update']]
        ),
        new Delete(
            security: "is_granted('SCENE_DELETE', object) and object == user"
        )
    ]
)]
class Scene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['scene:create'])]
    #[Assert\NotNull(groups: ['scene:create'])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotNull(groups: ['scene:create'])]
    #[Assert\Type(type: 'int', groups: ['scene:create'])]
    #[Assert\PositiveOrZero(groups: ['scene:create'])]
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
