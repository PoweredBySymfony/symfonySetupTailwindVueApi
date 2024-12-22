<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GenreMusicalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GenreMusicalRepository::class)]
class GenreMusical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["evenementMusical:read"])]
    private ?string $nom = null;

    /**
     * @var Collection<int, EvenementMusical>
     */
    #[ORM\ManyToMany(targetEntity: EvenementMusical::class, mappedBy: 'genreMuscical')]
    private Collection $evenementMusicals;

    public function __construct()
    {
        $this->evenementMusicals = new ArrayCollection();
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
            $evenementMusical->addGenreMuscical($this);
        }

        return $this;
    }

    public function removeEvenementMusical(EvenementMusical $evenementMusical): static
    {
        if ($this->evenementMusicals->removeElement($evenementMusical)) {
            $evenementMusical->removeGenreMuscical($this);
        }

        return $this;
    }
}
