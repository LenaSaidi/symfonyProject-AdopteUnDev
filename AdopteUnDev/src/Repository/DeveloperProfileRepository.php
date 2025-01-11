<?php

namespace App\Repository;

use App\Entity\DeveloperProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeveloperProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeveloperProfile::class);
    }

    // public function findMatchingProfiles(array $criteria): array
    public function findMatchingProfiles(array $criteria): array
    {
        $qb = $this->createQueryBuilder('d');

        if (!empty($criteria['technologies'])) {
            $qb->join('d.technologies', 't')
                ->andWhere('t.name IN (:technologies)')
                ->setParameter('technologies', $criteria['technologies']);
        }

        if (!empty($criteria['location'])) {
            $qb->andWhere('d.location = :location')
                ->setParameter('location', $criteria['location']);
        }

        if (!empty($criteria['minSalary'])) {
            $qb->andWhere('d.minSalary <= :minSalary')
                ->setParameter('minSalary', $criteria['minSalary']);
        }

        if (!empty($criteria['experienceLevel'])) {
            $qb->andWhere('d.experienceLevel = :experienceLevel')
                ->setParameter('experienceLevel', $criteria['experienceLevel']);
        }

        return $qb->getQuery()->getResult();
    }


public function findMostViewed(int $limit = 5): array
{
    return $this->createQueryBuilder('j')
        ->orderBy('j.views', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

    public function findMostViewedProfiles(int $limit = 5)
    {
        return $this->createQueryBuilder('dp')
            ->orderBy('dp.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findLatestProfiles(int $limit = 3)
    {
        return $this->createQueryBuilder('dp')
            ->orderBy('dp.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
}