<?php

namespace App\Controller;

use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubController extends AbstractController
{
    #[Route('/subscribers', name: 'all_sub')]
    public function index(SubscriptionRepository $repo): Response
    {
         $allsub = $repo->findAll();
        // dd($allsub);
        return $this->render('sub/index.html.twig', [
            'controller_name' => 'SubController',
           'Subscriptions' => $allsub
        ]);
    }
}
