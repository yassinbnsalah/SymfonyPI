<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SanteController extends AbstractController
{
    #[Route('/sante', name: 'app_sante')]
    public function index(): Response
    {
        return $this->render('sante/index.html.twig', [
            'controller_name' => 'SanteController',
        ]);
    }
    #[Route('/RendezVous/liste', name: 'rendezVousListe')]
    public function rendezVousListe(): Response
    {
        return $this->render('sante/RendezVous/listerendezvous.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/dashboard/doctor/rendez-vous', name: 'ListeRendezVous')]
    public function ListeRendezVous(): Response
    {

        $user = $this->getUser();
        return $this->render('user/doctor/Dashboarddoctor.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user
        ]);
    }
    #[Route('/dashboard/doctor/clientliste', name: 'ListeClientByDoctor')]
    public function ListeClientByDoctor(): Response
    {

        $user = $this->getUser();
        return $this->render('user/doctor/ListeClientDoctor.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user
        ]);
    }
    #[Route('/dashboard/doctor/disponibilityliste', name: 'ListeDisponibility')]
    public function ListeDisponibility(): Response
    {

        $user = $this->getUser();
        return $this->render('user/doctor/DisponibilityListe.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user
        ]);
    }
}
