<?php

namespace App\Service;

use App\Repository\DeveloperProfileRepository;
use App\Repository\JobOfferRepository;

class SearchService
{
    private DeveloperProfileRepository $developerProfileRepository;
    private JobOfferRepository $jobOfferRepository;

    public function __construct(
        DeveloperProfileRepository $developerProfileRepository,
        JobOfferRepository $jobOfferRepository
    ) {
        $this->developerProfileRepository = $developerProfileRepository;
        $this->jobOfferRepository = $jobOfferRepository;
    }

    public function searchByCriteria(array $criteria): array
    {
        $type = $criteria['type'] ?? 'all'; // Définit un type par défaut 'all' s'il n'est pas spécifié
    
        // Filtrage des résultats en fonction du type (developer, job ou all)
        if ($type === 'developer') {
            return [
                'developers' => $this->searchDevelopers($criteria),
                'jobs' => [],
            ];
        } elseif ($type === 'job') {
            return [
                'developers' => [],
                'jobs' => $this->searchJobs($criteria),
            ];
        } else {
            return [
                'developers' => $this->searchDevelopers($criteria),
                'jobs' => $this->searchJobs($criteria),
            ];
        }
    }
    

    private function searchDevelopers(array $criteria): array
    {
        return $this->developerProfileRepository->findByAdvancedCriteria(
            $criteria['technologies'],
            $criteria['location'],
            $criteria['minSalary'],
            $criteria['experienceLevel']
        );
    }

    private function searchJobs(array $criteria): array
    {
        return $this->jobOfferRepository->findByAdvancedCriteria(
            $criteria['technologies'],
            $criteria['location'],
            $criteria['minSalary'],
            $criteria['experienceLevel']
        );
    }
}
