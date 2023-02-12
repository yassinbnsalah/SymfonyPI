<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportController extends AbstractController
{
    #[Route('/sport', name: 'app_sport')]
    public function index(): Response
    {
        return $this->render('sport/index.html.twig', [
            'controller_name' => 'SportController',
        ]);
    }

    #[Route('/dashboard/coach/activity', name: 'activityListe')]
    public function activityListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/activityList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

    
    #[Route('/dashboard/coach/seances', name: 'seanceListe')]
    public function seanceListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/seanceList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

     
    #[Route('/dashboard/coach/planning', name: 'planningListe')]
    public function planningListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/planningList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

    #[Route('/dashboard/coach/client', name: 'CoachclientList')]
    public function CoachclientList(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/listClient.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }
}
