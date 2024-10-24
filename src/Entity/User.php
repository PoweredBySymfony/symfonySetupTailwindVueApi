<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $login = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $villeHabitation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDeNaissance = null;

    /**
     * @var Collection<int, EvenementMusical>
     */
    #[ORM\ManyToMany(targetEntity: EvenementMusical::class, mappedBy: 'participants')]
    private Collection $evenementMusicals;

    /**
     * @var Collection<int, PartieConcert>
     */
    #[ORM\OneToMany(targetEntity: PartieConcert::class, mappedBy: 'artiste', orphanRemoval: true)]
    private Collection $partieConcerts;

    public function __construct()
    {
        $this->evenementMusicals = new ArrayCollection();
        $this->partieConcerts = new ArrayCollection();
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
        // $this->plainPassword = null;
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
