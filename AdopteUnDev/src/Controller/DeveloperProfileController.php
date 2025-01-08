<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Form\DeveloperProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class DeveloperProfileController extends AbstractController
{
    #[Route('/developer/profle/create', name: 'developer_profile_create')]
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
            return $this->redirectToRoute('developer_profile_show', ['id' => $existingProfile->getId()]);
        }

        $profile = new DeveloperProfile();
        $profile->setUser($user);

        $form = $this->createForm(DeveloperProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profile);
            $em->flush();

            return $this->redirectToRoute('developer_profile_show', ['id' => $profile->getId()]);
        }

        return $this->render('developer_profile/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/developer/profile/{id}', name: 'developer_profile_show')]
    public function show(int $id, EntityManagerInterface $em)
    {
        $developerProfile = $em->getRepository(DeveloperProfile::class)->find($id);

        if (!$developerProfile) {
            throw $this->createNotFoundException('Profile not found');
        }

        return $this->render('developer_profile/show.html.twig', [
            'developerProfile' => $developerProfile,
        ]);
    }

    #[Route('/developer/profile/{id}/edit', name: 'developer_profile_edit')]
    public function edit(DeveloperProfile $developerProfile, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(DeveloperProfileType::class, $developerProfile);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();
    
            if ($avatar) {
                // Générez un nom unique pour le fichier
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Génère un nom de fichier sûr
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatar->guessExtension();
    
                // Déplacez le fichier dans le répertoire des avatars
                try {
                    $avatar->move(
                        $this->getParameter('avatars_directory'), // Le paramètre dans config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier ne peut pas être déplacé
                }
    
                // Mettre à jour le chemin de l'avatar dans l'entité
                $developerProfile->setAvatar($newFilename);
            }
    
            // Sauvegarde dans la base de données
            $em->flush();
    
            // Redirection vers la page du profil
            return $this->redirectToRoute('developer_profile_show', ['id' => $developerProfile->getId()]);
        }
    
        return $this->render('developer_profile/edit.html.twig', [
            'form' => $form->createView(),
            'developerProfile' => $developerProfile,
        ]);
    }
    

}

