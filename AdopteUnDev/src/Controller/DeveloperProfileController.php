<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Form\DeveloperProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperProfileController extends AbstractController
{
    #[Route('/developer/create', name: 'developer_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $developerProfile = new DeveloperProfile();
        $form = $this->createForm(DeveloperProfileType::class, $developerProfile);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer automatiquement l'utilisateur connecté
            $developerProfile->setUser($this->getUser());

            // Gestion de l'avatar (upload du fichier)
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $newFilename = uniqid() . '.' . $avatarFile->guessExtension();
                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'), // Chemin du répertoire défini dans services.yaml
                        $newFilename
                    );
                    $developerProfile->setAvatar($newFilename);
                } catch (FileException $e) {
                    // Gérer les erreurs d'upload
                }
            }

            // Sauvegarder le profil
            $em->persist($developerProfile);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('developer_profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
