<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RendezController extends AbstractController
{
    #[Route('/rendez', name: 'app_rendez')]
    public function index(): Response
    {
        return $this->render('rendez/index.html.twig', [
            'controller_name' => 'RendezController',
        ]);
    }
}
