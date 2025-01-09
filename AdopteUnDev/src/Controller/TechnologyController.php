<?php
// src/Controller/TechnologyController.php
namespace App\Controller;

use App\Entity\Technology;
use App\Form\TechnologyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechnologyController extends AbstractController
{

    #[Route('/technology', name: 'technology_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer toutes les technologies depuis la base de données
        $technologies = $em->getRepository(Technology::class)->findAll();

        return $this->render('technology/index.html.twig', [
            'technologies' => $technologies,
        ]);
    }


    #[Route('/technology/create', name: 'technology_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $technology = new Technology();
        $form = $this->createForm(TechnologyType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($technology);
            $em->flush();

            return $this->redirectToRoute('technology_index');
        }

        return $this->render('technology/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/technology/{id}/delete', name: 'technology_delete')]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $technology = $em->getRepository(Technology::class)->find($id);

        if ($technology) {
            $em->remove($technology);
            $em->flush();
        }

        return $this->redirectToRoute('technology_index');
    }
}
