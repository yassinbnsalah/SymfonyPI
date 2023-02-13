<?php

namespace App\Controller;

use App\Entity\Disponibility;
use App\Form\DisponibilityType;
use App\Repository\DisponibilityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function DeleteDisponibility($id , Request $req,ManagerRegistry $em, DisponibilityRepository $repo): Response
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
