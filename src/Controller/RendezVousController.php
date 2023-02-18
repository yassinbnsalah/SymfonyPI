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
    #[Route('/rendez-vous/add/{id}', name: 'addRendezVous1')]
    public function addRendezVous(Request $request,$id,ManagerRegistry $em): Response
        {
        $DoctorByID = $em->getRepository(User::class)->find($id) ;
        $rendezvous = new RendezVous();
        $user = $this->getUser();
        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);
        $rendezvous->setFromuser($user);
        if($form->isSubmitted() && $form->isValid()){
            $rendezvous->setState('inconfirmed') ; 
            $rendezvous->setDatePassageRV(new \DateTime()) ;

            $rendezvous->setFromuser($user);
            $rendezvous->setTodoctor($DoctorByID) ;
            $em=$em->getManager();
            $em->persist($rendezvous);
            $em->flush() ; 
            return $this->redirectToRoute('listeRendezVous');
        }
        return $this->render('sante/addrendezvous.html.twig', [
            'form' => $form->createView(),
            'doctor' => $DoctorByID,
        ]);

    }
   
    #[Route('/rendez-vous/confirme/{id}', name: 'confirmerendezvous')]
    public function confirmerendezvous($id , RendezVousRepository $repo, ManagerRegistry $em): Response
    {   
        $user = $this->getUser();
        $rdvtoconfirm = $repo->find($id); 
        $rdvtoconfirm->setState("confirm"); 
        $em = $em->getManager();  
        $em->persist($rdvtoconfirm); 
        $em->flush() ;
        return $this->redirectToRoute('listeRendezVousForDoctor');
    }
    #[Route('/rendez-vous/cancel/{id}', name: 'cancelrdv')]
    public function CancelRendezVous($id , RendezVousRepository $repo, ManagerRegistry $em): Response
    {
        $rdvtocancel = $repo->find($id); 
        $rdvtocancel->setState("Cancel");
        $em = $em->getManager();  
        $em->persist($rdvtocancel); 
        $em->flush() ;
        $user = $this->getUser(); 
        // if($user->getRoles()[0] == 'ROLE_DOCTOR'){
        //     return $this->redirectToRoute('rendevousListes', array('id' => $rdvtocancel->getTodoctor()->getId())); 
        // }
        if($user->getRoles()[0] == 'ROLE_MEDCIN'){
            return $this->redirectToRoute('listeRendezVousForDoctor');
        }else{
            return $this->redirectToRoute('rendezVousListe');
        }
     
    }

    #[Route('/client/rendez-vous/{id}', name: 'rendezdetails')]
    public function rendezdetails($id , RendezVousRepository $repo, ManagerRegistry $em): Response
    {
        $rdvdetails = $repo->find($id); 
        
        $user = $this->getUser(); 
      
       
        return $this->render('user/client/rendezvousdetails.html.twig', [
                'user' => $user, 
                'rendezVous' => $rdvdetails
        ]);
       
     
    }

    #[Route('/doctor/rendez-vous/{id}', name: 'doctorrendezdetails')]
    public function doctorrendezdetails($id , RendezVousRepository $repo, ManagerRegistry $em): Response
    {
        $rdvdetails = $repo->find($id); 
        
        $user = $this->getUser(); 
      
       
        return $this->render('user/doctor/rendezvousdetails.html.twig', [
                'user' => $user, 
                'rendezVous' => $rdvdetails
        ]);
       
     
    }
}

