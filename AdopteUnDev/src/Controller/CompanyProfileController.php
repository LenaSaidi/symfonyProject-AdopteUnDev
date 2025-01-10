<?php

namespace App\Controller;

use App\Entity\CompanyProfile;
use App\Entity\JobOffer; 
use App\Form\CompanyProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class CompanyProfileController extends AbstractController
{
    #[Route('/company/profile/create', name: 'company_profile_create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        // Vérification si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérification si un profil existe déjà
        $existingProfile = $em->getRepository(CompanyProfile::class)->findOneBy(['user' => $user]);

        if ($existingProfile) {
            return $this->redirectToRoute('company_profile_show', ['id' => $existingProfile->getId()]);
        }

        $profile = new CompanyProfile();
        $profile->setUser($user);

        $form = $this->createForm(CompanyProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profile);
            $em->flush();

            return $this->redirectToRoute('company_profile_show', ['id' => $profile->getId()]);
        }

        return $this->render('company_profile/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/company/profile/{id}', name: 'company_profile_show')]
    public function show(int $id, EntityManagerInterface $em)
    {
        $companyProfile = $em->getRepository(CompanyProfile::class)->find($id);

        if (!$companyProfile) {
            throw $this->createNotFoundException('Profile not found');
        }

        return $this->render('company_profile/show.html.twig', [
            'companyProfile' => $companyProfile,
        ]);
    }

    #[Route('/company/profile/{id}/edit', name: 'company_profile_edit')]
    public function edit(CompanyProfile $companyProfile, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CompanyProfileType::class, $companyProfile);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();
    
            if ($logo) {
                // Générez un nom unique pour le fichier
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Génère un nom de fichier sûr
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();
    
                // Déplacez le fichier dans le répertoire des avatars
                try {
                    $logo->move(
                        $this->getParameter('logos_directory'), // Le paramètre dans config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier ne peut pas être déplacé
                }
    
                // Mettre à jour le chemin de l'avatar dans l'entité
                $companyProfile->setLogo($newFilename);
            }
    
            // Sauvegarde dans la base de données
            $em->flush();
    
            // Redirection vers la page du profil
            return $this->redirectToRoute('company_profile_show', ['id' => $companyProfile->getId()]);
        }
    
        return $this->render('company_profile/edit.html.twig', [
            'form' => $form->createView(),
            'companyProfile' => $companyProfile,
        ]);
    }

    #[Route('/company/statistics', name: 'company_statistics')]
    #[IsGranted('ROLE_COMPANY')]
    public function statistics(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $jobOffers = $em->getRepository(JobOffer::class)->findBy(['user' => $user]);
    
        return $this->render('company_profile/statistics.html.twig', [
            'jobOffers' => $jobOffers,
        ]);
    }
    

}

