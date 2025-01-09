<?php

// src/Repository/TechnologyRepository.php
namespace App\Repository;

use App\Entity\Technology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TechnologyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    // Ajoutez ici vos méthodes personnalisées si nécessaire
}

