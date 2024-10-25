<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PartieConcertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PartieConcertRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ["groups" => ["partie_concert:create"]],
            normalizationContext: ["groups" => ["partie_concert:read"]],
        ),
        new Patch(
            denormalizationContext: ["groups" => ["partie_concert:update"]],
            normalizationContext: ["groups" => ["partie_concert:read"]],
            security: "is_granted('UTILISATEUR_EDIT', object) and object == user",
        ),
        new Delete(),
    ],
    normalizationContext: ["groups" => ["partie_concert:read"]],
)]
class PartieConcert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["scene:read", "partie_concert:read", 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["scene:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    private ?string $nomArtiste = null;

    #[ORM\Column]
    #[Groups(["scene:read", "partie_concert:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    private ?bool $artistePrincipal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["scene:read", "partie_concert:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    private ?\DateTimeInterface $dateDeDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["scene:read", "partie_concert:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    private ?\DateTimeInterface $dateDeFin = null;

    /**
     * @var Collection<int, Scene>
     */
    #[ORM\OneToMany(targetEntity: Scene::class, mappedBy: 'partieConcerts')]
    #[Groups(["scene:read", "partie_concert:read"])]
    private Collection $scenes;

    #[ORM\ManyToOne(inversedBy: 'partieConcerts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["partie_concert:read", 'partie_concert:create', 'partie_concert:update'])]
    private ?User $artiste = null;

    public function __construct()
    {
        $this->scenes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomArtiste(): ?string
    {
        return $this->nomArtiste;
    }

    public function setNomArtiste(string $nomArtiste): static
    {
        $this->nomArtiste = $nomArtiste;

        return $this;
    }

    public function isArtistePrincipal(): ?bool
    {
        return $this->artistePrincipal;
    }

    public function setArtistePrincipal(bool $artistePrincipal): static
    {
        $this->artistePrincipal = $artistePrincipal;

        return $this;
    }

    public function getDateDeDebut(): ?\DateTimeInterface
    {
        return $this->dateDeDebut;
    }

    public function setDateDeDebut(\DateTimeInterface $dateDeDebut): static
    {
        $this->dateDeDebut = $dateDeDebut;

        return $this;
    }

    public function getDateDeFin(): ?\DateTimeInterface
    {
        return $this->dateDeFin;
    }

    public function setDateDeFin(\DateTimeInterface $dateDeFin): static
    {
        $this->dateDeFin = $dateDeFin;

        return $this;
    }

    /**
     * @return Collection<int, Scene>
     */
    public function getScenes(): Collection
    {
        return $this->scenes;
    }

    public function addScene(Scene $scene): static
    {
        if (!$this->scenes->contains($scene)) {
            $this->scenes->add($scene);
            $scene->setPartieConcerts($this);
        }

        return $this;
    }

    public function removeScene(Scene $scene): static
    {
        if ($this->scenes->removeElement($scene)) {
            // set the owning side to null (unless already changed)
            if ($scene->getPartieConcerts() === $this) {
                $scene->setPartieConcerts(null);
            }
        }

        return $this;
    }

    public function getArtiste(): ?User
    {
        return $this->artiste;
    }

    public function setArtiste(?User $artiste): static
    {
        $this->artiste = $artiste;

        return $this;
    }
}
