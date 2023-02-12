<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\User;
use App\Form\SubscriptionType;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin', name: 'admindash')]
    public function index(): Response
    {
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/client/liste', name: 'listeClient')]
    public function listeClient(): Response
    {
        return $this->render('user/client/listeClient.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

   

    #[Route('/client/listesub', name: 'listeSubClient')]
    public function listeSubClient(): Response
    {
       $user = $this->getUser();
      
        //dd($this->getUser()); 
        return $this->render('user/client/clientdashsub.html.twig', [
            'controller_name' => 'UserController',
           'user' => $user
        ]);
    }

    #[Route('/client/{id}', name: 'clientDetails')]
    public function clientDetails(Request $req , $id , ManagerRegistry $em): Response
    {
        $ClientByID = $em->getRepository(User::class)->find($id) ;
        $sb = new Subscription() ; 
        $form = $this->createForm(SubscriptionType :: class , $sb);
        $form->handleRequest($req) ; 
        if($form->isSubmitted())
        {
            
            $date = \DateTime::createFromFormat('Y-m-d',$sb->getDateSub()->format('Y-m-d'));
            if($sb->getType() == '1')
            {
                $interval = new DateInterval('P30D');
                $date->add($interval);
                $sb->setDateExpire($date); 
            }
            else if ($sb->getType() == '2'){
                $interval = new DateInterval('P90D');
                $date->add($interval);
                $sb->setDateExpire($date); 
            }else{
                $interval = new DateInterval('P180D');
                $date->add($interval);
                $sb->setDateExpire($date); 
            }
            
            $sb->setUser($ClientByID) ; 
            $sb->setState("Confirmed"); 
            $em = $em->getManager(); 
            $em->persist($sb);
            $em->flush() ; 
        }
        return $this->render('user/client/clientdetails.html.twig', [
            'controller_name' => 'UserController',
            'client' => $ClientByID,
            'form' => $form->createView()
        ]);
    }
    #[Route('/doctor/liste', name: 'listeDoctor')]
    public function listeDoctor(): Response
    {
        return $this->render('user/doctor/listeDoctor.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/pharmacien/liste', name: 'listePharmaciens')]
    public function listePharmaciens(): Response
    {
        return $this->render('user/pharmacien/listePharmacien.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    
    #[Route('/coach/liste', name: 'listecoachs')]
    public function listecoachs(): Response
    {
        return $this->render('user/coach/listeCoach.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/home', name: '22')]
    public function home(): Response
    {
        return $this->render('/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
