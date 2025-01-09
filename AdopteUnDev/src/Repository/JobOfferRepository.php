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

    /**
     * Rechercher des offres d'emploi correspondant aux critères.
     *
     * @param array $criteria Les critères de recherche.
     * @return array Les offres d'emploi correspondantes.
     */
    public function findMatchingJobs(array $criteria): array
    {
        $qb = $this->createQueryBuilder('j');

        if (!empty($criteria['technologies'])) {
            $qb->join('j.technologies', 't')
               ->andWhere('t.name IN (:technologies)')
               ->setParameter('technologies', $criteria['technologies']);
        }

        if (!empty($criteria['location'])) {
            $qb->andWhere('j.location = :location')
               ->setParameter('location', $criteria['location']);
        }

        if (!empty($criteria['minSalary'])) {
            $qb->andWhere('j.minSalary >= :minSalary')
               ->setParameter('minSalary', $criteria['minSalary']);
        }

        if (!empty($criteria['experienceLevel'])) {
            $qb->andWhere('j.experienceLevel = :experienceLevel')
               ->setParameter('experienceLevel', $criteria['experienceLevel']);
        }

        return $qb->getQuery()->getResult();
    }
}
