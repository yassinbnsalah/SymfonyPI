<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubController extends AbstractController
{
    #[Route('/subbscribers', name: 'all_sub')]
    public function index(): Response
    {
        return $this->render('sub/index.html.twig', [
            'controller_name' => 'SubController',
        ]);
    }
}
