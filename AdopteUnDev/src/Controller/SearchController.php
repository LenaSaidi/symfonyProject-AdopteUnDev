<?php
// src/Controller/SearchController.php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Entity\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'advanced_search', methods: ['GET'])]
    public function showSearchForm(): Response
    {
        return $this->render('search/advanced_search.html.twig');
    }

    #[Route('/search/results', name: 'search_results', methods: ['GET'])]
    public function executeSearch(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les paramètres de recherche
        $technologiesInput = $request->query->get('technologies', '');
        $technologies = array_filter(array_map('trim', explode(',', $technologiesInput)));  // Séparer les technologies et filtrer les valeurs vides

        $type = $request->query->get('type', 'all');
        $location = $request->query->get('location', '');
        $minSalary = $request->query->get('minSalary', null);
        $experienceLevel = $request->query->get('experienceLevel', null);

        $results = [];

        // Recherche des développeurs
        if ($type === 'developer' || $type === 'all') {
            $qb = $em->createQueryBuilder();
            $qb->select('d')
                ->from(DeveloperProfile::class, 'd')
                ->join('d.technologies', 't');

            // Filtre pour technologies
            if (!empty($technologies)) {
                $qb->andWhere($qb->expr()->in('t.name', ':technologies'))
                    ->setParameter('technologies', $technologies);
            }

            // Filtre pour localisation
            if (!empty($location)) {
                $qb->andWhere('d.location LIKE :location')
                    ->setParameter('location', '%' . $location . '%');
            }

            // Filtre pour le salaire minimum
            if ($minSalary !== null) {
                $qb->andWhere('d.minSalary >= :minSalary')
                    ->setParameter('minSalary', $minSalary);
            }

            // Filtre pour le niveau d'expérience
            if ($experienceLevel !== null) {
                $qb->andWhere('d.experienceLevel >= :experienceLevel')
                    ->setParameter('experienceLevel', $experienceLevel);
            }

            $results['developers'] = $qb->getQuery()->getResult();
        }

        // Recherche des offres d'emploi
        if ($type === 'job' || $type === 'all') {
            $qb = $em->createQueryBuilder();
            $qb->select('j')
                ->from(JobOffer::class, 'j')
                ->join('j.technologies', 't');

            // Filtre pour technologies
            if (!empty($technologies)) {
                $qb->andWhere($qb->expr()->in('t.name', ':technologies'))
                    ->setParameter('technologies', $technologies);
            }

            // Filtre pour localisation
            if (!empty($location)) {
                $qb->andWhere('j.location LIKE :location')
                    ->setParameter('location', '%' . $location . '%');
            }

            // Filtre pour le salaire minimum
            if ($minSalary !== null) {
                $qb->andWhere('j.salary >= :minSalary')
                    ->setParameter('minSalary', $minSalary);
            }

            // Filtre pour le niveau d'expérience requis
            if ($experienceLevel !== null) {
                $qb->andWhere('j.experienceRequired >= :experienceLevel')
                    ->setParameter('experienceLevel', $experienceLevel);
            }

            $results['jobs'] = $qb->getQuery()->getResult();
        }

        return $this->render('search/results.html.twig', [
            'results' => $results,
        ]);
    }
}
