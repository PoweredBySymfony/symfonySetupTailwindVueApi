<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EvenementMusicalRepository;
use App\State\EvenementMusicalProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Link;

#[ORM\Entity(repositoryClass: EvenementMusicalRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/scenes/{idScene}/evenements',
            uriVariables: [
                'idScene' => new Link(toProperty: 'scenes', fromClass: Scene::class)
            ],
        ),
        new Get(),
        new Post(
            normalizationContext: ["groups" => ["evenementMusical:read"]],
            denormalizationContext: ["groups" => ["event_music:create"]],
            security: "is_granted('ROLE_ORGANIZER')",
            validationContext: ['groups' => ['evenement_musical:create']],
            processor: EvenementMusicalProcessor::class,
        ),
        new Patch(
            normalizationContext: ["groups" => ["evenementMusical:read"]],
            denormalizationContext: ["groups" => ["event_music:update"]],
            security: "is_granted('EVENEMENT_MUSICAL_EDIT', object) and object == user",
            validationContext: ['groups' => ['evenement_musical:update']],
            processor: EvenementMusicalProcessor::class
        ),
        new Delete(
            security: "is_granted('EVENEMENT_MUSICAL_DELETE', object)"
        ),
//        récupère tout les evenement auquel l'utilisateur participe
        new GetCollection(
            uriTemplate: '/users/{id}/evenements',
            uriVariables: [
                'id' => new Link(toProperty: 'participants', fromClass: User::class)
            ],
        ),
    ],
    normalizationContext: ['groups' => ['evenementMusical:read']],
    order: ["dateDeDebut" => "DESC"]
)]
class EvenementMusical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["scene:read", "evenementMusical:read", 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["scene:read", "evenementMusical:read", 'user:read', 'event_music:create', 'event_music:update'])]
    #[Assert\NotBlank(groups: ['evenement_musical:create'])]
    #[Assert\NotNull(groups: ['evenement_musical:create'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["scene:read", "evenementMusical:read", 'user:read', 'event_music:create', 'event_music:update'])]
    #[Assert\NotBlank(groups: ['evenement_musical:create'])]
    #[Assert\NotNull(groups: ['evenement_musical:create'])]
    private ?\DateTimeInterface $dateDeDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["scene:read", "evenementMusical:read", 'user:read', 'event_music:create', 'event_music:update'])]
    #[Assert\NotBlank(groups: ['evenement_musical:create'])]
    #[Assert\NotNull(groups: ['evenement_musical:create'])]
    private ?\DateTimeInterface $dateDeFin = null;

    #[ORM\Column]
    #[Groups(["scene:read", "evenementMusical:read", 'user:read', 'event_music:create', 'event_music:update'])]
    #[Assert\NotBlank(groups: ['evenement_musical:create'])]
    #[Assert\NotNull(groups: ['evenement_musical:create'])]
    #[Assert\PositiveOrZero(groups: ['evenement_musical:create'])]
    #[Assert\Type(type: 'float', groups: ['evenement_musical:create'])]
    private ?float $prix = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["scene:read", "evenementMusical:read", 'user:read', 'event_music:create', 'event_music:update'])]
    #[Assert\NotBlank(groups: ['evenement_musical:create'])]
    #[Assert\NotNull(groups: ['evenement_musical:create'])]
    private ?string $adresse = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'evenementMusicals')]
    #[Groups(['evenementMusical:read','event_music:create', 'event_music:update'])]
    private Collection $participants;

    /**
     * @var Collection<int, Scene>
     */
    /**
     * @var Collection<int, Scene>
     */
    #[ORM\OneToMany(targetEntity: Scene::class, mappedBy: 'evenementMusical', cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['evenementMusical:read', 'event_music:create', 'event_music:update'])]
    private Collection $scenes;


    #[ORM\ManyToOne(inversedBy: 'organisateurEvenementMuscial')]
    private ?User $organisateur = null;

    /**
     * @var Collection<int, GenreMusical>
     */
    #[ORM\ManyToMany(targetEntity: GenreMusical::class, inversedBy: 'evenementMusicals')]
    #[Groups(['evenementMusical:read'])]
    private Collection $genreMusical;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->scenes = new ArrayCollection();
        $this->genreMusical = new ArrayCollection();
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

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
            $scene->setEvenementMusical($this);
        }

        return $this;
    }

    public function removeScene(Scene $scene): static
    {
        if ($this->scenes->removeElement($scene)) {
            // set the owning side to null (unless already changed)
            if ($scene->getEvenementMusical() === $this) {
                $scene->setEvenementMusical(null);
            }
        }

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, GenreMusical>
     */
    public function getGenreMusical(): Collection
    {
        return $this->genreMusical;
    }

    public function addGenreMuscical(GenreMusical $genreMuscical): static
    {
        if (!$this->genreMusical->contains($genreMuscical)) {
            $this->genreMusical->add($genreMuscical);
        }

        return $this;
    }

    public function removeGenreMuscical(GenreMusical $genreMuscical): static
    {
        $this->genreMusical->removeElement($genreMuscical);

        return $this;
    }
}
