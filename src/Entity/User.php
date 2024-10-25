<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use App\State\UserProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", fields: ["email"])]
#[ApiResource(
    operations: [
        new Get(),
        new Delete(security: "is_granted('UTILISATEUR_EDIT', object) and object == user"),
        new Post(
            denormalizationContext: ["groups" => ["user:create"]],
            validationContext: ["groups" => ["Default", "user:create"]],
            processor: UserProcessor::class
        ),
        new Patch(
            denormalizationContext: ["groups" => ["user:update"]],
            security: "is_granted('UTILISATEUR_EDIT', object) and object == user",
            validationContext: ["groups" => ["Default", "user:update"]],
            processor: UserProcessor::class,
        ),
        new GetCollection()
    ],
    normalizationContext: ["groups" => ["user:read"]],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(groups: ["user:create"])]
    #[Assert\NotNull(groups: ["user:create"])]
    #[Assert\Length(min: 4, max: 20, minMessage: "Login trop court", maxMessage: "Login trop long")]
    #[Groups(['user:read', 'user:create',"partie_concert:read"])]
    private ?string $login = null;

    #[ORM\Column]
    private array $roles = [];

    #[ApiProperty(description: 'plainPassword property', readable: false)]
    #[NotBlank]
    #[NotNull]
    #[Length(min: 8, max: 30)]
    private ?string $plainPassword = null;

    #[UserPassword(groups: ["user:update"])]
    #[ApiProperty(readable: false)]
    #[Assert\NotBlank(groups: ["user:update"])]
    #[Assert\NotNull(groups: ["user:update"])]
    #[Groups(['user:update'])]
    private ?string $currentPlainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["user:create"])]
    #[Assert\NotNull(groups: ["user:create"])]
    #[Assert\Email(groups: ["user:create"])]
    #[Groups(['user:read','user:create', 'user:update',"partie_concert:read"])]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(groups: ["user:create"])]
    #[Assert\NotNull(groups: ["user:create"])]
    #[Groups(['user:create', 'user:update',"partie_concert:read",'user:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(groups: ["user:create"])]
    #[Assert\NotNull(groups: ["user:create"])]
    #[Groups(['user:create', 'user:update',"partie_concert:read",'user:read'])]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(groups: ["user:create"])]
    #[Assert\NotNull(groups: ["user:create"])]
    #[Groups(['user:create', 'user:update',"partie_concert:read",'user:read'])]
    private ?string $villeHabitation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(groups: ["user:create"])]
    #[Assert\NotNull(groups: ["user:create"])]
    #[Assert\DateTime(format: "Y-m-d", message: "La date de naissance doit être au format YYYY-MM-DD", groups: ["user:create"])]
    #[Groups(['user:create', 'user:update',"partie_concert:read",'user:read'])]
    private ?\DateTimeInterface $dateDeNaissance = null;

    // Définition de l'attribut mot de passe sans le groupe de dénormalisation
    #[ORM\Column]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $password = null;

    // Associez les autres attributs (collections) aux groupes de lecture
    #[ORM\ManyToMany(targetEntity: EvenementMusical::class, mappedBy: 'participants',)]
    #[Groups('user:read')]
    private Collection $evenementMusicals;

    #[ORM\OneToMany(targetEntity: PartieConcert::class, mappedBy: 'artiste', orphanRemoval: true)]
    #[Groups('user:read')]
    private Collection $partieConcerts;

    public function __construct()
    {
        $this->evenementMusicals = new ArrayCollection();
        $this->partieConcerts = new ArrayCollection();
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->currentPlainPassword = null;
        $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getVilleHabitation(): ?string
    {
        return $this->villeHabitation;
    }

    public function setVilleHabitation(string $villeHabitation): static
    {
        $this->villeHabitation = $villeHabitation;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): static
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * @return Collection<int, EvenementMusical>
     */
    public function getEvenementMusicals(): Collection
    {
        return $this->evenementMusicals;
    }

    public function addEvenementMusical(EvenementMusical $evenementMusical): static
    {
        if (!$this->evenementMusicals->contains($evenementMusical)) {
            $this->evenementMusicals->add($evenementMusical);
            $evenementMusical->addParticipant($this);
        }

        return $this;
    }

    public function removeEvenementMusical(EvenementMusical $evenementMusical): static
    {
        if ($this->evenementMusicals->removeElement($evenementMusical)) {
            $evenementMusical->removeParticipant($this);
        }

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
            $partieConcert->setArtiste($this);
        }

        return $this;
    }

    public function removePartieConcert(PartieConcert $partieConcert): static
    {
        if ($this->partieConcerts->removeElement($partieConcert)) {
            // set the owning side to null (unless already changed)
            if ($partieConcert->getArtiste() === $this) {
                $partieConcert->setArtiste(null);
            }
        }

        return $this;
    }
}
