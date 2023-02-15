<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous', name: 'app_rendez_vous')]
    public function index(): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'controller_name' => 'RendezVousController',
        ]);
    }
    #[Route('/rendezvous/add/{id}', name: 'listerendezvoust')]
    public function addRendezVous(Request $request,$id,ManagerRegistry $em): Response
        {
        $DoctorByID = $em->getRepository(User::class)->find($id) ;
        $rendezvous = new RendezVous();
        $user = $this->getUser();
        $form = $this->createForm(addRendezVouType::class, $rendezvous);
        $form->handleRequest($request);
        $rendezvous->setFromuser($user);
        if($form->isSubmitted() && $form->isValid()){
            $em=$em->getManager();
            $em->persist($rendezvous);
            $rendezvous->setFromuser($user);
            $rendezvous->setTodoctor($DoctorByID) ;
                return $this->redirectToRoute('listeRendezVous');
          }
        return $this->render('rndezvous/add.html.twig', [
            'form' => $form->createView(),
            'rendezvous' => $DoctorByID,
        ]);

    }


}
