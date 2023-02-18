<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
  
    

    #[Route('/', name: 'homepageVisitor')]
    public function homepageVisitor(): Response
    {   
        if($this->getUser()){
            return $this->render('home/home.html.twig', [
                'controller_name' => 'HomeController',
                'user' => $this->getUser()
            ]);
        }
        return $this->render('home/index.html.twig', [
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
        return $this->render('error-404.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
