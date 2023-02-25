<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
  
    

    #[Route('/', name: 'homepageVisitor')]
    public function homepageVisitor(ProduitRepository $Rep): Response
    {   
        $produits = $Rep->findAll();
        if($this->getUser()){
            return $this->render('home/home.html.twig', [
                'controller_name' => 'HomeController',
                'user' => $this->getUser(),
                'produits' => $produits 
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
   
    #[Route('/facture', name: 'facture')]
    public function pharmacien(): Response
    {
        return $this->render('/facture.html.twig', [
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
