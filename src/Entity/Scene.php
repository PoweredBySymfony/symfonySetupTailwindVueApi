<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SceneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SceneRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            normalizationContext: ["groups" => ["scene:read"]],
            denormalizationContext: ["groups" => ["scene:create"]],
            security: "is_granted('SCENE_EDIT', object)",
            validationContext: ['groups' => ['scene:create']],
        ),
        new Patch(
            normalizationContext: ["groups" => ["scene:read"]],
            denormalizationContext: ["groups" => ["scene:update"]],
            security: "is_granted('SCENE_EDIT', object)",
            validationContext: ['groups' => ['scene:update']],
        ),
        new Delete(
            security: "is_granted('SCENE_DELETE', object)"
        )
    ],
    normalizationContext: ["groups" => ["scene:read"]]
)]
class Scene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["scene:read", "partie_concert:read",'evenementMusical:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['scene:create'])]
    #[Assert\NotNull(groups: ['scene:create'])]
    #[Groups(["scene:read", "scene:create", "scene:update", "partie_concert:read"])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Groups(["scene:read", "scene:create", "scene:update", "partie_concert:read"])]
    #[Assert\NotNull(groups: ['scene:create'])]
    #[Assert\Type(type: 'int', groups: ['scene:create'])]
    #[Assert\PositiveOrZero(groups: ['scene:create'])]
    private ?int $nombreMaxParticipants = null;

    #[ORM\ManyToOne(inversedBy: 'scenes')]
    #[Groups(["scene:read", "partie_concert:read", "scene:create", "scene:update"])]
    private ?EvenementMusical $evenementMusical = null;

    /**
     * @var Collection<int, PartieConcert>
     */
    #[ORM\OneToMany(targetEntity: PartieConcert::class, mappedBy: 'scene', cascade: ['remove'],orphanRemoval: true)]
    #[Groups(['scene:read', 'scene:create', 'evenementMusical:read'])]
    private Collection $partieConcerts;

    public function __construct()
    {
        $this->partieConcerts = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, PartieConcert>
     */
    public function getPartieConcerts(): Collection
    {
        return $this->partieConcerts;
    }

    public function addPartieConcert(PartieConcert $partieConcert): static
    {
        if (!$this->partieConcerts->contains($partieConcert)) {
            $this->partieConcerts->add($partieConcert);
            $partieConcert->setScene($this);
        }

        return $this;
    }

    public function removePartieConcert(PartieConcert $partieConcert): static
    {
        if ($this->partieConcerts->removeElement($partieConcert)) {
            // set the owning side to null (unless already changed)
            if ($partieConcert->getScene() === $this) {
                $partieConcert->setScene(null);
            }
        }

        return $this;
    }
}
