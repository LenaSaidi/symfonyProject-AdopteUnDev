<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\DeveloperProfile;
use App\Entity\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends AbstractController
{
    // Ajouter un développeur aux favoris
    #[Route('/favorite/developer/{id}', name: 'add_favorite_developer', methods: ['POST'])]
    public function addFavoriteDeveloper(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le profil développeur
        $developerProfile = $entityManager->getRepository(DeveloperProfile::class)->find($id);
        if (!$developerProfile) {
            throw $this->createNotFoundException('Developer profile not found.');
        }

        // Vérifier si le développeur est déjà dans les favoris
        $existingFavorite = $entityManager->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'developerProfile' => $developerProfile,
        ]);

        if (!$existingFavorite) {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setDeveloperProfile($developerProfile);

            $entityManager->persist($favorite);
            $entityManager->flush();

            $this->addFlash('success', 'Developer added to favorites!');
        } else {
            $this->addFlash('info', 'This developer is already in your favorites.');
        }

        return $this->redirectToRoute('developer_profile', ['id' => $id]);
    }

    // Retirer un développeur des favoris
    #[Route('/favorite/developer/{id}/remove', name: 'remove_favorite_developer', methods: ['POST'])]
    public function removeFavoriteDeveloper(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le profil développeur
        $developerProfile = $entityManager->getRepository(DeveloperProfile::class)->find($id);
        if (!$developerProfile) {
            throw $this->createNotFoundException('Developer profile not found.');
        }

        // Vérifier si le favori existe
        $existingFavorite = $entityManager->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'developerProfile' => $developerProfile,
        ]);

        if ($existingFavorite) {
            $entityManager->remove($existingFavorite);
            $entityManager->flush();

            $this->addFlash('success', 'Developer removed from favorites!');
        } else {
            $this->addFlash('info', 'This developer was not in your favorites.');
        }

        return $this->redirectToRoute('developer_profile', ['id' => $id]);
    }



    // Ajouter une offre d'emploi aux favoris
    #[Route('/favorite/job-offer/{id}', name: 'add_favorite_job_offer')]
    public function addFavoriteJobOffer(int $id, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $jobOffer = $em->getRepository(JobOffer::class)->find($id);
        if (!$jobOffer) {
            throw $this->createNotFoundException('Offre d\'emploi introuvable.');
        }

        $existingFavorite = $em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'jobOffer' => $jobOffer,
        ]);
        
        if (!$existingFavorite) {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setJobOffer($jobOffer);
            $em->persist($favorite);
            $em->flush();
        }

        return $this->redirectToRoute('job_offer_show', ['id' => $id]);
    }

    // Retirer une offre d'emploi des favoris
    #[Route('/favorite/job-offer/{id}/remove', name: 'remove_favorite_job_offer')]
    public function removeFavoriteJobOffer(int $id, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $jobOffer = $em->getRepository(JobOffer::class)->find($id);
        if (!$jobOffer) {
            throw $this->createNotFoundException('Offre d\'emploi introuvable.');
        }

        $existingFavorite = $em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'jobOffer' => $jobOffer,
        ]);

        if ($existingFavorite) {
            $em->remove($existingFavorite);
            $em->flush();
        }

        return $this->redirectToRoute('job_offer_show', ['id' => $id]);
    }


    #[Route('/favorite/list', name: 'favorite_list')]
    public function listFavorites(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        // Récupérer les favoris liés à l'utilisateur connecté
        $favorites = $em->getRepository(Favorite::class)->findBy(['user' => $user]);
    
        return $this->render('favorite/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }
    

}
