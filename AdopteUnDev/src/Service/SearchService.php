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
        $type = $criteria['type'] ?? 'all';

        if ($type === 'developer') {
            return [
                'developers' => $this->developerProfileRepository->findMatchingProfiles($criteria),
                'jobs' => [],
            ];
        } elseif ($type === 'job') {
            return [
                'developers' => [],
                'jobs' => $this->jobOfferRepository->findMatchingJobs($criteria),
            ];
        } else {
            return [
                'developers' => $this->developerProfileRepository->findMatchingProfiles($criteria),
                'jobs' => $this->jobOfferRepository->findMatchingJobs($criteria),
            ];
        }
    }
}
