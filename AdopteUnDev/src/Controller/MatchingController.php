<?php
namespace App\Controller;

use App\Service\MatchingService;
use App\Entity\JobOffer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatchingController extends AbstractController
{
    private MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    #[Route('/match/profiles', name: 'suggest_profiles')]
    public function matchProfiles(Request $request): Response
    {
        dump($request->query->all()); // Pour les paramètres GET
        dump($request->request->all()); // Pour les paramètres POST
        dump($request->getContent()); // Pour le corps brut (JSON, etc.)
        
        // Extraire les critères de la requête
        $criteria = [
            'technologies' => $request->query->all('technologies'),
            'location' => $request->query->get('location', null),
            'minSalary' => (int) $request->query->get('minSalary', 0),
            'experienceLevel' => (int) $request->query->get('experienceLevel', 0),
        ];

        dump($criteria);

        // Ici, vous devrez peut-être créer un objet JobOffer à partir des critères.
        $jobOffer = new JobOffer();
        $jobOffer->setTechnology($criteria['technology']);
        $jobOffer->setLocation($criteria['location']);
        $jobOffer->setSalary($criteria['minSalary']);
        $jobOffer->setExperienceRequired($criteria['experienceLevel']);

        // Appeler le service de matching
        $matchingProfiles = $this->matchingService->createMatchesForJobOffer($jobOffer);
    
        // Renvoyer les résultats au template
        return $this->render('matching/profiles.html.twig', [
            'profiles' => $matchingProfiles,
        ]);
    }

    #[Route('/match/jobs', name: 'suggest_jobs')]
    public function matchJobs(Request $request): Response
    {
        $criteria = [
            'technologies' => $request->query->all('technology'),
            'location' => $request->query->get('location'),
            'minSalary' => $request->query->get('minSalary'),
            'experienceLevel' => $request->query->get('experienceLevel'),
        ];

        // Vous devez créer un DeveloperProfile basé sur les critères pour appeler le service.
        $developerProfile = new DeveloperProfile();
        $developerProfile->setTechnology($criteria['technology']);
        $developerProfile->setLocation($criteria['location']);
        $developerProfile->setMinSalary($criteria['minSalary']);
        $developerProfile->setExperienceLevel($criteria['experienceLevel']);

        $jobs = $this->matchingService->createMatchesForProfile($developerProfile);

        return $this->render('matching/jobs.html.twig', [
            'jobs' => $jobs,
        ]);
    }
}
