<?php

// src/Entity/Evaluation.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    // L'ID de l'évaluation
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    // La note attribuée
    #[ORM\Column(type: "integer")]
    private ?int $rating = null;

    // Le développeur qui a laissé l'évaluation
    #[ORM\ManyToOne(targetEntity: "App\Entity\DeveloperProfile")]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeveloperProfile $evaluator = null;

    // Le développeur qui reçoit l'évaluation
    #[ORM\ManyToOne(targetEntity: "App\Entity\DeveloperProfile")]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeveloperProfile $evaluatedDeveloper = null;

    // La date de l'évaluation
    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getEvaluator(): ?DeveloperProfile
    {
        return $this->evaluator;
    }

    public function setEvaluator(?DeveloperProfile $evaluator): self
    {
        $this->evaluator = $evaluator;

        return $this;
    }

    public function getEvaluatedDeveloper(): ?DeveloperProfile
    {
        return $this->evaluatedDeveloper;
    }

    public function setEvaluatedDeveloper(?DeveloperProfile $evaluatedDeveloper): self
    {
        $this->evaluatedDeveloper = $evaluatedDeveloper;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
