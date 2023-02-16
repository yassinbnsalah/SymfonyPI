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
        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);
        $rendezvous->setFromuser($user);
        if($form->isSubmitted() && $form->isValid()){
            $em=$em->getManager();
            $em->persist($rendezvous);
            $rendezvous->setFromuser($user);
            $rendezvous->setTodoctor($DoctorByID) ;
                return $this->redirectToRoute('listeRendezVous');
          }
        return $this->render('rendezvous/addrendezvous.html.twig', [
            'form' => $form->createView(),
            'rendezvous' => $DoctorByID,
        ]);

    }
   
    #[Route('/rendezvous/confirme/{id}', name: 'confirmerendezvous')]
    public function confirmerendezvous($id , RendezVousRepository $repo, ManagerRegistry $em): Response
    {   
        $user = $this->getUser();
        $rdvtoconfirm = $repo->find($id); 
        $rdvtoconfirm->setState("confirm"); 
        $em = $em->getManager();  
        $em->persist($rdvtoconfirm); 
        $em->flush() ;
        return $this->redirectToRoute('rendevousListes', array('id' => $rdvtoconfirm->getFromuser()->getId())); 
    }
    #[Route('/rendezvous/cancel/{id}', name: 'cancelrdv')]
    public function CancelRendezVous($id , RendezVousRepository $repo, ManagerRegistry $em): Response
    {
        $rdvtocancel = $repo->find($id); 
        $rdvtocancel->setState("Cancel");
        $em = $em->getManager();  
        $em->persist($rdvtocancel); 
        $em->flush() ;
        $user = $this->getUser(); 
        if($user->getRoles()[0] == 'ROLE_DOCTOR'){
            return $this->redirectToRoute('rendevousListes', array('id' => $rdvtocancel->getTodoctor()->getId())); 
        }
       
    }
}

