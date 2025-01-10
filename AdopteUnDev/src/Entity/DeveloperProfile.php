<?php

namespace App\Entity;

use App\Repository\DeveloperProfileRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeveloperProfileRepository::class)]
class DeveloperProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\ManyToMany(targetEntity: Technology::class)]
    #[ORM\JoinTable(name: 'developerProfile_technologies')]
    private Collection $technologies;

    #[ORM\Column]
    private ?int $experienceLevel = null;

    #[ORM\Column(nullable: true)]
    private ?int $minSalary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'integer')]
    private $views = 0;
    

    public function __construct()
    {
        $this->technologies = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist] // Ajoutez cette méthode pour définir la date de création juste avant l'enregistrement
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime(); 
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getExperienceLevel(): ?int
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(int $experienceLevel): static
    {
        $this->experienceLevel = $experienceLevel;

        return $this;
    }

    public function getMinSalary(): ?int
    {
        return $this->minSalary;
    }

    public function setMinSalary(?int $minSalary): static
    {
        $this->minSalary = $minSalary;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getTechnologies(): Collection
    {
        return $this->technologies;
    }

    public function addTechnology(Technology $technology): static
    {
        if (!$this->technologies->contains($technology)) {
            $this->technologies->add($technology);
        }

        return $this;
    }

    public function removeTechnology(Technology $technology): static
    {
        $this->technologies->removeElement($technology);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }
    
    public function incrementViews(): self
    {
        $this->views++;
        return $this;
    }
}
