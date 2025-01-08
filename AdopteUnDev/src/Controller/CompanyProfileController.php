<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompanyProfileController extends AbstractController
{
    #[Route('/company/profile', name: 'app_company_profile')]
    public function index(): Response
    {
        return $this->render('company_profile/index.html.twig', [
            'controller_name' => 'CompanyProfileController',
        ]);
    }
}
