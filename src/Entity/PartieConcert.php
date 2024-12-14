<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PartieConcertRepository;
use App\State\EvenementMusicalProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Link;

#[ORM\Entity(repositoryClass: PartieConcertRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/scenes/{idScene}/partieConcerts',
            uriVariables: [
                'idScene' => new Link(
                    fromProperty: 'partieConcerts',
                    fromClass: Scene::class
                ),
            ],
        ),
        new Get(),
        new Post(
            normalizationContext: ["groups" => ["partie_concert:read"]],
            denormalizationContext: ["groups" => ["partie_concert:create"]],
            security: "is_granted('PARTIE_CONCERT_EDIT', object)",
            validationContext: ['groups' => ['partie_concert:create']],
            processor: EvenementMusicalProcessor::class,
        ),
        new Patch(
            normalizationContext: ["groups" => ["partie_concert:read"]],
            denormalizationContext: ["groups" => ["partie_concert:update"]],
            security: "is_granted('PARTIE_CONCERT_EDIT', object)",
            validationContext: ['groups' => ['partie_concert:update']],
            processor: EvenementMusicalProcessor::class,
        ),
        new Delete(
            security: "is_granted('PARTIE_CONCERT_DELETE', object)"
        )
    ],
    normalizationContext: ["groups" => ["partie_concert:read"]],
    order: ["dateDeDebut" => "DESC"]
)]
class PartieConcert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["scene:read", "partie_concert:read", 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['partie_concert:create'])]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    #[Groups(["scene:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    #[Assert\Type(type: 'bool', groups: ['partie_concert:create'])]
    #[Groups(["scene:read", "partie_concert:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    private ?bool $artistePrincipal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["scene:read", "partie_concert:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    #[Assert\NotBlank(groups: ['partie_concert:create'])]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    private ?\DateTimeInterface $dateDeDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["scene:read", "partie_concert:read", 'user:read', 'partie_concert:create', 'partie_concert:update'])]
    #[Assert\NotBlank(groups: ['partie_concert:create'])]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    private ?\DateTimeInterface $dateDeFin = null;


    #[ORM\ManyToOne(inversedBy: 'partieConcerts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["partie_concert:read", 'partie_concert:create', 'partie_concert:update'])]
    private ?User $artiste = null;

    #[ORM\ManyToOne(inversedBy: 'partieConcerts')]
    private ?Scene $scene = null;

    public function __construct()
    {}

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

    public function getArtiste(): ?User
    {
        return $this->artiste;
    }

    public function setArtiste(?User $artiste): static
    {
        $this->artiste = $artiste;

        return $this;
    }

    public function getScene(): ?Scene
    {
        return $this->scene;
    }

    public function setScene(?Scene $scene): static
    {
        $this->scene = $scene;

        return $this;
    }
}
