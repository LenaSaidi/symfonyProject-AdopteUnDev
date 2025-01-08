<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeveloperProfileController extends AbstractController
{
    #[Route('/developer/profile', name: 'app_developer_profile')]
    public function index(): Response
    {
        return $this->render('developer_profile/index.html.twig', [
            'controller_name' => 'DeveloperProfileController',
        ]);
    }
}
