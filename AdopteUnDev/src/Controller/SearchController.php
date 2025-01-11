<?php

namespace App\Controller;

use App\Service\SearchService;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    #[Route('/search', name: 'advanced_search', methods: ['GET'])]
    public function showSearchForm(): Response
    {
        return $this->render('search/advanced_search.html.twig');
    }

    #[Route('/search/results', name: 'search_results', methods: ['GET'])]
    public function executeSearch(Request $request): Response
    {
        $criteria = [
            'type' => $request->query->get('type', 'all'),
            'technologies' => $request->query->all('technology'),
            'location' => $request->query->get('location'),
            'minSalary' => (int)$request->query->get('minSalary', 0),
            'experienceLevel' => (int)$request->query->get('experienceLevel', 0),
        ];

        $results = $this->searchService->searchByCriteria($criteria);

        return $this->render('search/results.html.twig', [
            'criteria' => $criteria,
            'results' => $results,
        ]);
    }

    #[Route('/search/job-offers', name: 'search_job_offers', methods: ['GET'])]
    public function searchJobOffers(Request $request, JobOfferRepository $jobOfferRepository): Response
    {
        $criteria = [
            'location' => $request->query->get('location'),
            'technologies' => $request->query->all('technology'),
            'minSalary' => $request->query->get('minSalary'),
            'experienceLevel' => $request->query->get('experienceLevel'),
        ];

        $jobOffers = $jobOfferRepository->findByAdvancedCriteria($criteria);

        return $this->render('job_offer/search_results.html.twig', [
            'jobOffers' => $jobOffers,
        ]);
    }
}
