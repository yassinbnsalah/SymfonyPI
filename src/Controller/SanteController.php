<?php

namespace App\Controller;

use App\Entity\Disponibility;
use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\DisponibilityType;
use App\Form\RendezVousType;
use App\Repository\DisponibilityRepository;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SanteController extends AbstractController
{
    #[Route('/sante', name: 'app_sante')]
    public function index(): Response
    {
        return $this->render('sante/index.html.twig', [
            'controller_name' => 'SanteController',
        ]);
    }
    #[Route('/rendezvous/liste', name: 'rendezVousListe')]
    public function rendezVousListe(RendezVousRepository $repo): Response
    {
        $usercurrent = $this->getUser();
        $rendezvous = $repo->findAll() ; 
        return $this->render('user/client/listerendezvousclient.html.twig', [
            'controller_name' => 'UserController',
            'user' => $usercurrent ,
            'rendezvous' => $rendezvous
        ]);
    }

    // HNEEE YE AMIRA AHAYAY
    #[Route('/sante/rendezvous/add/{id}', name: 'addRendezVous')]
    
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
            return $this->redirectToRoute('rendezVousListe');
        }
        return $this->render('sante/addrendezvous.html.twig', [
            'form' => $form->createView(),
            'doctor' => $DoctorByID,
        ]);
    }


    #[Route('/dashboard/doctor/rendez-vous', name: 'listeRendezVousForDoctor')]
    public function ListeRendezVous(RendezVousRepository $repo): Response
    {

        $usercurrent = $this->getUser();
        $rendezvous = $repo->findAll() ; 
        if($usercurrent->getRoles()[0] == 'ROLE_ADMIN'){
            return $this->render('sante/rendezvous/listerendezvous.html.twig', [
                'controller_name' => 'HomeController',
                'user' => $usercurrent ,
                'rendezvous' => $rendezvous
            ]);
        }else{
            return $this->render('user/doctor/Dashboarddoctor.html.twig', [
                'controller_name' => 'HomeController',
                'user' => $usercurrent ,
                'rendezvous' => $rendezvous
            ]);
        }
    
    }

    
    #[Route('/dashboard/doctor/clientliste', name: 'ListeClientByDoctor')]
    public function ListeClientByDoctor(UserRepository $userRepository): Response
    {
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = $this->getUser();
        return $this->render('user/doctor/ListeClientDoctor.html.twig', [
            'controller_name' => 'HomeController',
            'User_client' => $User_client,
            'user' => $user
        ]);
    }
    #[Route('/dashboard/doctor/disponibilityliste', name: 'ListeDisponibility')]
    public function ListeDisponibility(Request $req,ManagerRegistry $em): Response
    {
        $dispo = new Disponibility() ; 
        $form = $this->createForm(DisponibilityType::class, $dispo);
        $form->handleRequest($req) ; 

        $user = $this->getUser();

        if($form->isSubmitted()){
            $dispo->setDoctor($user);
            $em = $em->getManager(); 
            $em->persist($dispo);
            $em->flush() ; 
        }
        return $this->render('user/doctor/DisponibilityListe.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    #[Route('/dashboard/doctor/disponibility/update/{id}', name: 'UpdateDisponibility')]
    public function UpdateDisponibility($id , Request $req,ManagerRegistry $em, DisponibilityRepository $repo): Response
    {
        $dispo = $repo->find($id) ; 
        $form = $this->createForm(DisponibilityType::class, $dispo);
        $form->handleRequest($req) ; 

        $user = $this->getUser();

        if($form->isSubmitted()){
            $dispo->setDoctor($user);
            $em = $em->getManager(); 
            $em->persist($dispo);
            $em->flush() ; 
            return $this->redirectToRoute('ListeDisponibility'); 
        } 
        return $this->render('user/doctor/updateDisponibility.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    #[Route('/dashboard/doctor/disponibility/delete/{id}', name: 'DeleteDisponibility')]
    public function DeleteDisponibility($id ,ManagerRegistry $em, DisponibilityRepository $repo): Response
    {
            $dispo = $repo->find($id) ; 
            $user = $dispo->getDoctor(); 
            $em = $em->getManager(); 
            $em->remove($dispo);
            $em->flush() ; 
          
            $userConnected = $this->getUser(); 
            if ($userConnected->getRoles()[0] == 'ROLE_ADMIN'){
                return $this->redirectToRoute('DoctorDetails', array('id' => $user->getId())); 
            }
            return $this->redirectToRoute('ListeDisponibility'); 
       
    }
}
