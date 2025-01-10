<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Form\DeveloperProfileType;
use App\Form\EvaluationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MatchingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\DeveloperProfileRepository;
use App\Repository\EvaluationRepository;
use App\Entity\Evaluation;


class DeveloperProfileController extends AbstractController
{
    private $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    #[Route('/developer-profiles', name: 'developer_profile_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les profils des développeurs depuis la base de données
        $developerProfiles = $entityManager->getRepository(DeveloperProfile::class)->findAll();

        return $this->render('developer_profile/index.html.twig', [
            'developerProfiles' => $developerProfiles,
        ]);
    }

    #[Route('/developer/profile/create', name: 'developer_profile_create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        // Vérification si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérification si un profil existe déjà
        $existingProfile = $em->getRepository(DeveloperProfile::class)->findOneBy(['user' => $user]);

        if ($existingProfile) {
            return $this->redirectToRoute('my_profile_show');
        }

        $profile = new DeveloperProfile();
        $profile->setUser($user);

        $form = $this->createForm(DeveloperProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profile);
            $em->flush();

            return $this->redirectToRoute('my_profile_show');
        }

        return $this->render('developer_profile/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/developer/profile/edit', name: 'developer_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $developerProfile = $em->getRepository(DeveloperProfile::class)->findOneBy(['user' => $user]);

        if (!$developerProfile) {
            return $this->redirectToRoute('developer_profile_create');
        }

        $form = $this->createForm(DeveloperProfileType::class, $developerProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();

            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatar->guessExtension();

                try {
                    $avatar->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                $developerProfile->setAvatar($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('my_profile_show');
        }

        return $this->render('developer_profile/edit.html.twig', [
            'form' => $form->createView(),
            'developerProfile' => $developerProfile,
        ]);
    }

    #[Route('/developer/myProfile', name: 'my_profile_show')]
    public function showProfile(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $developerProfile = $em->getRepository(DeveloperProfile::class)->findOneBy(['user' => $user]);

        if ($developerProfile) {
            $matchingJobOffers = $this->matchingService->createMatchesForProfile($developerProfile);

            return $this->render('developer_profile/profile.html.twig', [
                'developerProfile' => $developerProfile,
                'matchingJobOffers' => $matchingJobOffers
            ]);
        } else {
            return $this->redirectToRoute('developer_profile_create');
        }
    }

    #[Route('/developer/profile/view/{id}', name: 'developer_profile_show')]
    public function showById(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer le profil du développeur par ID
        $developerProfile = $em->getRepository(DeveloperProfile::class)->find($id);

        if (!$developerProfile) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        // Vous pouvez également récupérer les offres d'emploi correspondantes ici si nécessaire
        // $matchingJobOffers = $this->matchingService->getMatchingJobOffers($developerProfile);

        return $this->render('developer_profile/show_by_id.html.twig', [
            'developerProfile' => $developerProfile,
            // 'matchingJobOffers' => $matchingJobOffers
        ]);
    }


    #[Route('/developer/{id}/evaluate', name: 'developer_evaluate')]
    public function evaluate($id,EntityManagerInterface $em, Request $request, DeveloperProfileRepository $developerProfileRepository, EvaluationRepository $evaluationRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $evaluator = $em->getRepository(DeveloperProfile::class)->findOneBy(['user' => $user]);

        if (!$evaluator) {
            return $this->redirectToRoute('login');
        }
        // Récupérer le développeur à évaluer
        $developer = $developerProfileRepository->find($id);

        if (!$developer) {
            throw $this->createNotFoundException('Développeur non trouvé');
        }

        $evaluation = $evaluationRepository->findOneBy([
            'evaluator' => $evaluator,
            'evaluatedDeveloper' => $developer,
        ]);

        if (!$evaluation) {
            // Créer un objet Evaluation
            $evaluation = new Evaluation();
            $evaluation->setEvaluator($evaluator); // Utilisateur connecté
            $evaluation->setEvaluatedDeveloper($developer);

        }

        // Créer et traiter le formulaire
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'évaluation dans la base de données
            $em->persist($evaluation);
            $em->flush();

            // Rediriger ou afficher un message de succès
            return $this->redirectToRoute('developer_profile', ['id' => $id]);
        }

        return $this->render('developer_profile/evaluate.html.twig', [
            'form' => $form->createView(),
            'developer' => $developer,
        ]);
    }

    #[Route('/developer/{id}/profile', name: 'developer_profile')]
    public function profile($id, DeveloperProfileRepository $developerProfileRepository, EvaluationRepository $evaluationRepository): Response
    {
        $developer = $developerProfileRepository->find($id);

        // Récupérer toutes les évaluations pour ce développeur
        $evaluations = $evaluationRepository->findBy(['evaluatedDeveloper' => $developer]);

        // Calculer la moyenne des évaluations
        $averageRating = 0;
        if (count($evaluations) > 0) {
            $sum = 0;
            foreach ($evaluations as $evaluation) {
                $sum += $evaluation->getRating();
            }
            $averageRating = $sum / count($evaluations);
        }

        return $this->render('developer_profile/profile2.html.twig', [
            'developer' => $developer,
            'averageRating' => $averageRating,
            'evaluations' => $evaluations,
        ]);
    }


}
