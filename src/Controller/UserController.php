<?php

namespace App\Controller;

use App\Entity\Disponibility;
use DateInterval;
use App\Entity\User;
use App\Form\addType;
use App\Entity\Subscription;
use App\Form\DisponibilityType;
use App\Form\SubscriptionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    #[Route('/admin', name: 'admindash')]
    public function index(UserRepository $userRepository)
    {  $User_admin = $userRepository->findByRole('["ROLE_ADMIN"]');
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'UserController',
            'User_admin' => $User_admin
        ]);
    }
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    #[Route('/client/liste', name: 'listeClient')]
    public function listeClient(UserRepository $userRepository ,Request $request, EntityManagerInterface $manager)
    {    
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $user->setRoles(['ROLE_CLIENT']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listeClient');
          }
        return $this->render('user/client/listeClient.html.twig', [
            'controller_name' => 'UserController',
            'User_client' => $User_client,
            'form' => $form->createView()
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


    #[Route('/client/update', name: 'UpdateClientData')]
    public function UpdateClientData(): Response
    {
        $user= $this->getUser(); 
       
        return $this->render('user/client/clientUpdateProfile.html.twig', [
            'controller_name' => 'UserController',
           'user' =>$user
        ]);
    }

    #[Route('/dashboard/client/update/{id}', name: 'UpdateClientDashboard')]
    public function UpdateClientDashboard($id, ManagerRegistry $em): Response
    {
        $user = $em->getRepository(User::class)->find($id) ;
        
        return $this->render('user/client/clientUpdateDash.html.twig', [
            'controller_name' => 'UserController',
           'user' =>$user
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
    public function listeDoctor(UserRepository $userRepository ,Request $request, EntityManagerInterface $manager)
    {   $User_medcin = $userRepository->findByRole('["ROLE_MEDCIN"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $user->setRoles(['ROLE_MEDCIN']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listeDoctor');
          }
        return $this->render('user/doctor/listeDoctor.html.twig', [
            'controller_name' => 'UserController',
            'User_medcin' => $User_medcin,
            'form' => $form->createView()
        ]);
    }

    #[Route('/doctor/{id}', name: 'DoctorDetails')]
    public function DoctorDetails($id , UserRepository $userRepository , Request $req , ManagerRegistry $em)
    {   $Doctor = $userRepository->find($id);
        $dispo = new Disponibility() ; 
        $form = $this->createForm(DisponibilityType::class, $dispo);
        $form->handleRequest($req) ; 

        $user = $this->getUser();

        if($form->isSubmitted()){
            $dispo->setDoctor($Doctor);
            $em = $em->getManager(); 
            $em->persist($dispo);
            $em->flush() ; 
        }
        return $this->render('user/doctor/DoctorDetailsDashboard.html.twig', [
            'controller_name' => 'UserController',
            'doctor' => $Doctor,
            'form' => $form->createView()
        ]);
    }

    #[Route('/pharmacien/liste', name: 'listePharmaciens')]
    public function listePharmaciens(UserRepository $userRepository ,Request $request, EntityManagerInterface $manager)
    {   $User_pharmacien = $userRepository->findByRole('["ROLE_PHARMACIEN"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $user->setRoles(['ROLE_PHARMACIEN']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listePharmaciens');
          }
        return $this->render('user/pharmacien/listePharmacien.html.twig', [
            'controller_name' => 'UserController',
            'User_pharmacien' => $User_pharmacien,
            'form' => $form->createView()
        ]);
    }


    
    #[Route('/coach/liste', name: 'listecoachs')]
    public function listecoachs(UserRepository $userRepository ,Request $request, EntityManagerInterface $manager)
    {   $User_coach = $userRepository->findByRole('["ROLE_COACH"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $user->setRoles(['ROLE_COACH']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listecoachs');
          }
        return $this->render('user/coach/listeCoach.html.twig', [
            'controller_name' => 'UserController',
            'User_coach' => $User_coach,
            'form' => $form->createView()
        ]);
    }
    #[Route('/home', name: '22')]
    public function home(): Response
    {
        return $this->render('/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
    #[Route('/update/{id}', name: 'updateUser')]
    public function  updateClassroom(ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $user = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $form = $this->createForm(ClassroomType::class, $user);
        $form->add('update', SubmitType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted())
        { $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('listeClient');
        }
        return $this->renderForm("user/update.html.twig",
            ["f"=>$form]) ;


    }
   #[Route("client/delete/{id}", name:'deleteClient')]
    public function deleteClient($id, ManagerRegistry $doctrine)
    {   
        $c = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();
        
        return $this->redirectToRoute('listeClient');
    }
    #[Route("coach/delete/{id}", name:'deleteCoach')]
    public function deleteCoach($id, ManagerRegistry $doctrine)
    {   
        $c = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();
        
        return $this->redirectToRoute('listecoachs');
    }
    #[Route("doctor/delete/{id}", name:'deleteDoctor')]
    public function deleteDoctor($id, ManagerRegistry $doctrine)
    {   
        $c = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();
        
        return $this->redirectToRoute('listeDoctor');
    }
    #[Route("pharmacien/delete/{id}", name:'deletePharmaciens')]
    public function deletePharmaciens($id, ManagerRegistry $doctrine)
    {   
        $c = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();
        
        return $this->redirectToRoute('listePharmaciens');
    }

    
    
}