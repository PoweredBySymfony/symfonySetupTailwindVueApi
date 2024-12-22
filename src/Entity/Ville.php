<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            denormalizationContext: ['groups' => ['ville:create']],
            validationContext: ['groups' => ['Default', 'ville:create']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['ville:update']],
            validationContext: ['groups' => ['Default', 'ville:update']]
        ),
    ],
    normalizationContext: ['groups' => ['ville:read']],
)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'ville:read', 'ville:create', 'ville:update'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['ville:create', 'ville:update'])]
    #[Assert\NotNull(groups: ['ville:create', 'ville:update'])]
    #[Groups(['user:read', 'ville:read', 'ville:create', 'ville:update'])]
    private ?string $nom = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'villeHabitation')]
    #[Groups(['ville:read'])]
    private Collection $users;

    #[ORM\Column]
    #[Assert\NotBlank(groups: ['ville:create', 'ville:update'])]
    #[Assert\NotNull(groups: ['ville:create', 'ville:update'])]
    #[Groups(['ville:read', 'ville:create', 'ville:update'])]
    private ?int $codePostal = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotNull(groups: ['ville:create', 'ville:update'])]
    #[Groups(['ville:read', 'ville:create', 'ville:update'])]
    private ?string $departement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['ville:create', 'ville:update'])]
    #[Assert\NotNull(groups: ['ville:create', 'ville:update'])]
    #[Groups(['ville:read', 'ville:create', 'ville:update'])]
    private ?string $pays = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setVilleHabitation($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getVilleHabitation() === $this) {
                $user->setVilleHabitation(null);
            }
        }

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): static
    {
        $this->departement = $departement;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }
}
