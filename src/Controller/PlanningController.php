<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Seance;
use App\Repository\ActivityRepository;
use App\Repository\PlanningRepository;
use App\Repository\SeanceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning')]
    public function index(): Response
    {
        return $this->render('planning/index.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }

    #[Route('/dashboard/coach/planning', name: 'planning_list')]
    public function planningListe(ActivityRepository $actRepo, SeanceRepository $seaRepo, PlanningRepository $plaRepo): Response
    {
        // Récupérer les données à partir de la base de données
        $user = $this->getUser();
        $activites = $actRepo->findAll();
        $seances = $seaRepo->findAll();
        $planning = $plaRepo->findAll();
        // Rendre la réponse en utilisant le template html.twig
        return $this->render('user/coach/planningList.html.twig', [
            'user' => $user,
            'activites' => $activites,
            'seances' => $seances,
            'planning' => $planning
        ]);
    }
}
