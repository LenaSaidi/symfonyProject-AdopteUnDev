<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Favorite;
use App\Entity\Matching;
use App\Form\JobOfferType;
use App\Service\MatchingService;
use App\Repository\JobOfferRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobOfferController extends AbstractController
{

    private $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    #[Route('/job-offers', name: 'job_offer_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $jobOffers = $em->getRepository(JobOffer::class)->findAll();

        return $this->render('job_offer/index.html.twig', [
            'jobOffers' => $jobOffers,
        ]);
    }


    #[Route('/company/job-offer', name: 'company_job_offer_index')]
    public function companyJobOffers(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos offres.');
        }

        $jobOffers = $em->getRepository(JobOffer::class)->findBy(['user' => $user]);

        return $this->render('job_offer/company_index.html.twig', [
            'jobOffers' => $jobOffers,
        ]);
    }

    #[Route('/job-offer/create', name: 'job_offer_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une offre.');
        }

        $jobOffer = new JobOffer();
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobOffer->setUser($user); // Associe l'utilisateur connecté à l'offre
            $em->persist($jobOffer);
            $em->flush();


            // Créer dynamiquement des correspondances pour cette offre d'emploi
            $matches = $this->matchingService->createMatchesForJobOffer($jobOffer);

            // return $this->redirectToRoute('company_job_offer_index');

            // Retourner la liste des correspondances ou rediriger vers une autre page
            return $this->render('job_offer/matches.html.twig', [
                'jobOffer' => $jobOffer,
                'matches' => $matches
            ]);
        }

        return $this->render('job_offer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/job-offer/{id}/edit', name: 'job_offer_edit')]
    public function edit(JobOffer $jobOffer, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($jobOffer->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres offres.');
        }

        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('company_job_offer_index');
        }

        return $this->render('job_offer/edit.html.twig', [
            'form' => $form->createView(),
            'jobOffer' => $jobOffer,
        ]);
    }

    #[Route('/job-offer/{id}/show', name: 'job_offer_show')]
    public function show(JobOffer $jobOffer ,EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        if ($user && $jobOffer->getUser() !== $user) {
            $jobOffer->incrementViews();
            $em->persist($jobOffer);
            $em->flush();
        }

        $isFavorite = $em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'jobOffer' => $jobOffer,
        ]);

        return $this->render('job_offer/show.html.twig', [
            'jobOffer' => $jobOffer,
            'isFavorite' => $isFavorite !== null, // true si l'offre est un favori
        ]);
    }

    #[Route('/job-offer/{id}/delete', name: 'job_offer_delete', methods: ['POST'])]
    public function delete(JobOffer $jobOffer, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($jobOffer->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres offres.');
        }
    
        // Supprimer les correspondances liées à cette offre d'emploi
        $matches = $em->getRepository(Matching::class)->findBy(['jobOffer' => $jobOffer]);
        foreach ($matches as $match) {
            $em->remove($match);  // Supprimer chaque correspondance
        }
    
        // Supprimer l'offre d'emploi elle-même
        $em->remove($jobOffer);
        $em->flush();
    
        return $this->redirectToRoute('company_job_offer_index');
    }
    
    #[Route('/jobOffer/{id}/matches', name: 'job_offer_matches')]
    public function showMatches(JobOffer $jobOffer): Response
    {
        // Appeler la méthode qui génère les correspondances
        $matches = $this->matchingService->createMatchesForJobOffer($jobOffer);

        return $this->render('job_offer/matches.html.twig', [
            'jobOffer' => $jobOffer,
            'matches' => $matches
        ]);
    }


    #[Route('/job/{id}/apply', name: 'job_offer_apply')]
    public function apply(
        int $id,
        JobOfferRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        $jobOffer = $repository->find($id);
    
        if (!$jobOffer) {
            throw $this->createNotFoundException('Offre non trouvée.');
        }
    
        // Incrémenter la popularité
        $jobOffer->setPopularity($jobOffer->getPopularity() + 1);
        $entityManager->flush();
    
        $this->addFlash('success', 'Votre candidature a été envoyée avec succès.');
    
        return $this->redirectToRoute('job_offer_show', ['id' => $id]);
    }
    
}
