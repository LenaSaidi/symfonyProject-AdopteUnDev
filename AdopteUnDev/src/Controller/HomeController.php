<?php

namespace App\Controller;

use App\Repository\DeveloperProfileRepository;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(
        DeveloperProfileRepository $developerProfileRepository,
        JobOfferRepository $jobOfferRepository
    ): Response {
        $user = $this->getUser();

        if ($user && $this->isGranted('ROLE_COMPANY')) {
            // Page d'accueil pour entreprises
            $mostViewedProfiles = $developerProfileRepository->findMostViewedProfiles();
            $latestProfiles = $developerProfileRepository->findLatestProfiles();

            return $this->render('home/company_home.html.twig', [
                'mostViewedProfiles' => $mostViewedProfiles,
                'latestProfiles' => $latestProfiles,
            ]);
        }

        if ($user && $this->isGranted('ROLE_DEVELOPER')) {
            // Page d'accueil pour développeurs
            $mostPopularOffers = $jobOfferRepository->findMostPopularOffers();
            $latestOffers = $jobOfferRepository->findLatestOffers();

            return $this->render('home/developer_home.html.twig', [
                'mostPopularOffers' => $mostPopularOffers,
                'latestOffers' => $latestOffers,
            ]);
        }

        // Page d'accueil par défaut si non connecté
        return $this->render('home/index.html.twig');
    }
}

