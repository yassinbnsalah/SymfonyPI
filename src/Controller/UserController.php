<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin', name: 'admindash')]
    public function index(): Response
    {
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/client/liste', name: 'listeClient')]
    public function listeClient(): Response
    {
        return $this->render('user/client/listeClient.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }



    #[Route('/doctor/liste', name: 'listeDoctor')]
    public function listeDoctor(): Response
    {
        return $this->render('user/doctor/listeDoctor.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/pharmacien/liste', name: 'listePharmaciens')]
    public function listePharmaciens(): Response
    {
        return $this->render('user/pharmacien/listePharmacien.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    
    #[Route('/coach/liste', name: 'listecoachs')]
    public function listecoachs(): Response
    {
        return $this->render('user/coach/listeCoach.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/home', name: '22')]
    public function home(): Response
    {
        return $this->render('/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
