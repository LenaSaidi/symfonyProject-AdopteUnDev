<?php

namespace App\Service;

use App\Entity\DeveloperProfile;
use App\Entity\JobOffer;
use App\Entity\Matching;
use Doctrine\ORM\EntityManagerInterface;

class MatchingService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createMatchesForJobOffer(JobOffer $jobOffer): array
    {
        $profiles = $this->entityManager->getRepository(DeveloperProfile::class)->findAll();
        $matches = [];
    
        foreach ($profiles as $profile) {
            $score = 0;
    
            $profileTechnologies = array_unique($profile->getTechnologies()->map(fn($tech) => $tech->getName())->toArray());
            $jobOfferTechnologies = array_unique($jobOffer->getTechnologies()->map(fn($tech) => $tech->getName())->toArray());
    
            if (array_intersect($jobOfferTechnologies, $profileTechnologies)) {
                $score += 1;
            }
    
            if ($profile->getLocation() === $jobOffer->getLocation()) {
                $score += 1;
            }
    
            if ($profile->getMinSalary() >= $jobOffer->getSalary()) {
                $score += 1;
            }
    
            if ($profile->getExperienceLevel() >= $jobOffer->getExperienceRequired()) {
                $score += 1;
            }
    
            if ($score > 0) {
                $matching = new Matching();
                $matching->setDeveloperProfile($profile);
                $matching->setJobOffer($jobOffer);
                $matching->setScore($score);
                $matches[] = $matching;
            }
        }
    
        usort($matches, fn($a, $b) => $b->getScore() <=> $a->getScore());
    
        foreach ($matches as $match) {
            $this->entityManager->persist($match);
        }
    
        $this->entityManager->flush();
    
        return $matches;
    }
    

    public function createMatchesForProfile(DeveloperProfile $developerProfile): array
    {
        // Récupérer toutes les offres d'emploi
        $jobOffers = $this->entityManager->getRepository(JobOffer::class)->findAll();

        $matchingOffers = [];
        foreach ($jobOffers as $jobOffer) {
            $score = 0;

            // Vérifier la correspondance sur la localisation
            if ($developerProfile->getLocation() === $jobOffer->getLocation()) {
                $score += 1;
            }

            // Vérifier la correspondance sur les technologies
            $commonTechnologies = array_intersect(
                array_unique($developerProfile->getTechnologies()->map(fn($tech) => $tech->getName())->toArray()),
                array_unique($jobOffer->getTechnologies()->map(fn($tech) => $tech->getName())->toArray())
            );

            if (count($commonTechnologies) > 0) {
                $score += count($commonTechnologies); // Plus il y a de technologies communes, plus le score est élevé
            }

            // Vérifier la correspondance sur le niveau d'expérience
            if ($developerProfile->getExperienceLevel() >= $jobOffer->getExperienceRequired()) {
                $score += 1;
            }

            // Vérifier la correspondance sur le salaire minimum
            if ($developerProfile->getMinSalary() >= $jobOffer->getSalary()) {
                $score += 1;
            }

            // Si le score est positif, ajouter l'offre d'emploi à la liste des offres correspondantes
            if ($score > 0) {
                $matchingOffers[] = [
                    'jobOffer' => $jobOffer,
                    'score' => $score
                ];
            }
        }

        // Trier les offres d'emploi en fonction du score (du plus élevé au plus bas)
        usort($matchingOffers, fn($a, $b) => $b['score'] <=> $a['score']);

        return $matchingOffers;
    }
}
