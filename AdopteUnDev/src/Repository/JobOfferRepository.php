<?php

namespace App\Repository;

use App\Entity\JobOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }
    
    public function findJobOffersWithTechnologies()
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.technologies', 't') // Jointure avec la table technologies
            ->addSelect('t'); // Ajouter les technologies aux résultats

        return $qb->getQuery()->getResult();
    }



    public function findMatchingJobs(array $criteria): array
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.technologies', 't')
            ->addSelect('t');

        if (!empty($criteria['technologies'])) {
            $qb->andWhere('t.name IN (:technologies)')
                ->setParameter('technologies', $criteria['technologies']);
        }

        if (!empty($criteria['location'])) {
            $qb->andWhere('j.location = :location')
                ->setParameter('location', $criteria['location']);
        }

        if (!empty($criteria['minSalary'])) {
            $qb->andWhere('j.salary >= :minSalary')
                ->setParameter('minSalary', $criteria['minSalary']);
        }

        if (!empty($criteria['experienceLevel'])) {
            $qb->andWhere('j.experienceRequired >= :experienceLevel')
                ->setParameter('experienceLevel', $criteria['experienceLevel']);
        }

        return $qb->getQuery()->getResult();
    }
    

    public function findMostPopularOffers(int $limit = 5)
    {
        return $this->createQueryBuilder('jo')
            ->orderBy('jo.popularity', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findLatestOffers(int $limit = 3)
    {
        return $this->createQueryBuilder('jo')
            ->orderBy('jo.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findMostViewed(int $limit = 5): array
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
    
    
}
