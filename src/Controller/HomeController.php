<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    { 
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/', name: 'homepageVisitor')]
    public function homepageVisitor(): Response
    { 
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/zaza', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('home/admin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/medcin', name: 'app_medcin')]
    public function medcin(): Response
    {
        return $this->render('home/medcin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/coach', name: 'app_coach')]
    public function coach(): Response
    {
        return $this->render('home/coach.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/pharmacien', name: 'app_pharmacien')]
    public function pharmacien(): Response
    {
        return $this->render('home/pharmacien.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/error', name: 'app_error')]
    public function error(): Response
    { 
        return $this->render('error/error-404.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
