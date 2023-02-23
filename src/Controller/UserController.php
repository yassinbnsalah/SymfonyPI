<?php

namespace App\Controller;
### yessine
use DateInterval;
use App\Entity\User;
use App\Form\addType;
use App\Form\ProfileType;
use App\Entity\Subscription;
use App\Entity\Disponibility;
use App\Form\SubscriptionType;
use App\Form\DisponibilityType;
use App\Repository\UserRepository;
use App\Repository\OrdennanceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    #[Route('/admin', name: 'admindash')]
    public function index(UserRepository $userRepository)
    {
        $User_admin = $userRepository->findByRole('["ROLE_ADMIN"]');
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'UserController',
            'User_admin' => $User_admin
        ]);
    }

    #[Route('/client/showClient', name: 'showClient')]
    public function showClient(UserRepository $userRepository)
    {
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = $this->getUser();
        return $this->render('user/client/showClient.html.twig', [
            'controller_name' => 'UserController',
            'User_client' => $User_client,
            'user' => $user
        ]);
    }
    #[Route('/coach/showCoach', name: 'showCoach')]
    public function showCoach(UserRepository $userRepository)
    {
        $User_coach = $userRepository->findByRole('["ROLE_COACH"]');
        $user = $this->getUser();
        return $this->render('user/coach/showCoach.html.twig', [
            'controller_name' => 'UserController',
            'User_coach' => $User_coach,
            'user' => $user
        ]);
    }
    #[Route('/doctor/showDoctor', name: 'showDoctor')]
    public function showDoctor(UserRepository $userRepository)
    {
        $User_doctor = $userRepository->findByRole('["ROLE_MEDCIN"]');
        $user = $this->getUser();

        return $this->render('user/doctor/showDoctor.html.twig', [
            'controller_name' => 'UserController',
            'User_doctor' => $User_doctor,
            'user' => $user
        ]);
    }
    #[Route('/pharmacien/showPharmacien', name: 'showPharmacien')]
    public function showPharmacien(UserRepository $userRepository)
    {
        $User_pharmacien = $userRepository->findByRole('["ROLE_PHARMACIEN"]');
        $user = $this->getUser();
        return $this->render('user/pharmacien/showPharmacien.html.twig', [
            'controller_name' => 'UserController',
            'User_pharmacien' => $User_pharmacien,
            'user' => $user
        ]);
    }
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    #[Route('/client/liste', name: 'listeClient')]
    public function listeClient(UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user->setRoles(['ROLE_CLIENT']);

                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listeClient');
            } else {
                $data = "Failed to add new Client  please click in ";
                $response = new JsonResponse($data);
                return $this->render('user/client/listeClient.html.twig', [
                    'controller_name' => 'UserController',
                    'User_client' => $User_client,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/client/listeClient.html.twig', [
            'controller_name' => 'UserController',
            'User_client' => $User_client,
            'data' => "",
            'form' => $form->createView()
        ]);
    }



    #[Route('/client/listesub', name: 'listeSubClient')]
    public function listeSubClient(): Response
    {
        $user = $this->getUser();
        return $this->render('user/client/clientdashsub.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }
    #[Route('/pharmacien/dashboard', name: 'dashPharmacien')]
    public function dashPharmacien(OrdennanceRepository $repo): Response
    {
        $usercurrent = $this->getUser();
        $ordonnance = $repo->findAll() ; 
           
        return $this->render('user/pharmacien/pharmacienDash.html.twig', [
            'controller_name' => 'UserController',
            'user' => $usercurrent ,
            'ordonnances' => $ordonnance
        ]);
    }

    #[Route('/client/update/{id}', name: 'UpdateClientData')]
    public function UpdateClientData($id, ManagerRegistry $doctrine,Request  $request): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
       
            /** @var UploadedFile $imageFile */
       
                   $imageFile = $form->get('image')->getData();

                     if($imageFile){
                         $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                              $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                     try {
                         $imageFile->move(
                             $this->getParameter('images_directory'),
                              $newFilename
                              );
                     } catch (FileException $e) {
                      // ... handle exception if something happens during file upload
                     }
                        $user->setImage($newFilename);
                        $em = $doctrine->getManager();
                        $em->flush();
      
                      }
               
            }
     return $this->render('user/client/clientUpdateProfile.html.twig', [
    'controller_name' => 'UserController',
    'user' =>$user,
    'data' => "",
    'form' => $form->createView()
        ]);
}

    
    #[Route('/doctor/update/{id}', name: 'UpdateDoctorData')]
    public function UpdateDoctorData($id, ManagerRegistry $doctrine,Request  $request): Response
     
        {
            $user = $doctrine->getRepository(User::class)->find($id);
            $form = $this->createForm(ProfileType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted()){
           
                /** @var UploadedFile $imageFile */
           
            $imageFile = $form->get('image')->getData();

             if($imageFile){
                 $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                 $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                 try {
                     $imageFile->move(
                         $this->getParameter('images_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
                 $user->setImage($newFilename);
                $em = $doctrine->getManager();
                $em->flush();
            }
            }
              return $this->render('user/doctor/doctorUpdateProfile.html.twig', [
                'controller_name' => 'UserController',
                'user' =>$user,
                'data' => "",
                'form' => $form->createView()
            ]);
        }
    
    #[Route('/coach/update/{id}', name: 'UpdateCoachData')]
    public function UpdateCoachData($id, ManagerRegistry $doctrine,Request  $request): Response
     
        {
            $user = $doctrine->getRepository(User::class)->find($id);
            $form = $this->createForm(ProfileType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted()){
                /** @var UploadedFile $imageFile */
           
            $imageFile = $form->get('image')->getData();

            if($imageFile){
                 $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                 $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                 try {
                     $imageFile->move(
                         $this->getParameter('images_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
                 $user->setImage($newFilename);
                $em = $doctrine->getManager();
                $em->flush();
          
            }
        }
              return $this->render('user/coach/coachUpdateProfile.html.twig', [
                'controller_name' => 'UserController',
                'user' =>$user,
                'data' => "",
                'form' => $form->createView()
            ]);
        }
    
    #[Route('/pharmacien/update/{id}', name: 'UpdatePharmacienData')]
    public function UpdatePharmacienData($id, ManagerRegistry $doctrine,Request  $request): Response
     
        {
            $user = $doctrine->getRepository(User::class)->find($id);
            $form = $this->createForm(ProfileType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted()){
                /** @var UploadedFile $imageFile */
           
            $imageFile = $form->get('image')->getData();

            if($imageFile){
                 $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                 $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                 try {
                     $imageFile->move(
                         $this->getParameter('images_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
                 $user->setImage($newFilename);
                $em = $doctrine->getManager();
                $em->flush();
          
            }
        }
              return $this->render('user/pharmacien/pharmacienUpdateProfile.html.twig', [
                'controller_name' => 'UserController',
                'user' =>$user,
                'data' => "",
                'form' => $form->createView()
            ]);
        }
    #[Route('/client/{id}', name: 'clientDetails')]
    public function clientDetails(Request $req, $id, ManagerRegistry $em): Response
    {
        $ClientByID = $em->getRepository(User::class)->find($id);
        $sb = new Subscription();
        $sb->setDateSub(new \DateTime());
        $form = $this->createForm(SubscriptionType::class, $sb);
        $form->handleRequest($req);
        $data = null ; 
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $date = \DateTime::createFromFormat('Y-m-d', $sb->getDateSub()->format('Y-m-d'));
                if ($sb->getType() == '1') {
                    $interval = new DateInterval('P30D');
                    $date->add($interval);
                    $sb->setDateExpire($date);
                } else if ($sb->getType() == '2') {
                    $interval = new DateInterval('P90D');
                    $date->add($interval);
                    $sb->setDateExpire($date);
                } else {
                    $interval = new DateInterval('P180D');
                    $date->add($interval);
                    $sb->setDateExpire($date);
                }

                $sb->setUser($ClientByID);
                $sb->setState("Confirmed");
                $em = $em->getManager();
                $em->persist($sb);
                $em->flush();
            } else {
                $data = "Failed to add new Sub to this client please click in ";
                // $response = new JsonResponse($data);
                return $this->render('user/client/clientdetails.html.twig', [
                    'controller_name' => 'UserController',
                    'client' => $ClientByID,
                    'data' =>  $data,
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/client/clientdetails.html.twig', [
            'controller_name' => 'UserController',
            'client' => $ClientByID,
            'data' => $data , 
            'form' => $form->createView()
        ]);
    }

    #[Route('/doctor/liste', name: 'listeDoctor')]
    public function listeDoctor(UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $User_medcin = $userRepository->findByRole('["ROLE_MEDCIN"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() ){
            if($form->isValid()){
                $user->setRoles(['ROLE_MEDCIN']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listeDoctor');
            }else{
                $data = "Failed to add new Doctor  please click in ";
                $response = new JsonResponse($data);
                return $this->render('user/doctor/listeDoctor.html.twig', [
                    'controller_name' => 'UserController',
                    'User_medcin' => $User_medcin,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
              }}
              return $this->render('user/doctor/listeDoctor.html.twig', [
                'controller_name' => 'UserController',
                'User_medcin' => $User_medcin,
                'data'=>"",
                'form' => $form->createView()
            ]);
        }
    
    

    #[Route('/doctor/{id}', name: 'DoctorDetails')]
    public function DoctorDetails($id, UserRepository $userRepository, Request $req, ManagerRegistry $em)
    {
        $Doctor = $userRepository->find($id);
        $dispo = new Disponibility();
        $dispo->setDateDispo(new \DateTime()); 
        $form = $this->createForm(DisponibilityType::class, $dispo);
        $form->handleRequest($req);
        $data = null ; 
      

        if ($form->isSubmitted()) {
            if($form->isValid()){
                $dispo->setDoctor($Doctor);
                $em = $em->getManager();
                $em->persist($dispo);
                $em->flush();
            }
            else{
                $data = "Failed to add new Disponibility Slote to this Doctor please click in ";
                // $response = new JsonResponse($data);
                return $this->render('user/doctor/DoctorDetailsDashboard.html.twig', [
                    'controller_name' => 'UserController',
                    'doctor' => $Doctor,
                    'data' =>  $data,
                    'form' => $form->createView()
                ]);
            }
           
        }
        return $this->render('user/doctor/DoctorDetailsDashboard.html.twig', [
            'controller_name' => 'UserController',
            'doctor' => $Doctor,
            'data' =>  $data,
            'form' => $form->createView()
        ]);
    }

    #[Route('/pharmacien/liste', name: 'listePharmaciens')]
    public function listePharmaciens(UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $User_pharmacien = $userRepository->findByRole('["ROLE_PHARMACIEN"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() ){
            if($form->isValid()){
                $user->setRoles(['ROLE_PHARMACIEN']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listePharmaciens');
            }else{
                $data = "Failed to add new Pharmacien  please click in ";
                $response = new JsonResponse($data);
                return $this->render('user/pharmacien/listePharmacien.html.twig', [
                    'controller_name' => 'UserController',
                    'User_pharmacien' => $User_pharmacien,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
              }}
              return $this->render('user/pharmacien/listePharmacien.html.twig', [
                'controller_name' => 'UserController',
                'User_pharmacien' => $User_pharmacien,
                'data'=>"",
                'form' => $form->createView()
            ]);
        }
   



    #[Route('/coach/liste', name: 'listecoachs')]
    public function listecoachs(UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $User_coach = $userRepository->findByRole('["ROLE_COACH"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() ){
            if($form->isValid()){
                $user->setRoles(['ROLE_COACH']);
              
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('listecoachs');
            }else{
                $data = "Failed to add new Coach  please click in ";
                $response = new JsonResponse($data);
                return $this->render('user/coach/listeCoach.html.twig', [
                    'controller_name' => 'UserController',
                    'User_coach' => $User_coach,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
              }}
              return $this->render('user/coach/listeCoach.html.twig', [
                'controller_name' => 'UserController',
                'User_coach' => $User_coach,
                'data'=>"",
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



    #[Route("client/delete/{id}", name: 'deleteClient')]
    public function deleteClient($id, ManagerRegistry $doctrine)
    {
        // First, delete the child records
        $subscriptions = $doctrine->getRepository(Subscription::class)->findBy(['user' => $id]);
        foreach ($subscriptions as $subscription) {

            $em = $doctrine->getManager();
            $em->remove($subscription);
        }
        // Then, delete the parent record
        $user = $doctrine->getRepository(User::class)->find($id);
        $em = $doctrine->getManager();
        $em->remove($user);
        // Finally, flush the changes to the database
        $em->flush();
        return $this->redirectToRoute('listeClient');
    }

    #[Route("coach/delete/{id}", name: 'deleteCoach')]
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
    #[Route("doctor/delete/{id}", name: 'deleteDoctor')]
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
    #[Route("pharmacien/delete/{id}", name: 'deletePharmaciens')]
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
    #[Route('/dashboard/client/update/{id}', name: 'UpdateClientDashboard')]
    public function UpdateClientDashboard($id, ManagerRegistry $doctrine, Request  $request): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($form->isValid()){
        $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('listeClient');
            
          }else{
            $data = "Failed to Update Client";
            $response = new JsonResponse($data);
            return $this->render('user/client/clientUpdateDash.html.twig', [
                'controller_name' => 'UserController',
                'data' => $response->getContent(),
                'form' => $form->createView()
            ]);
          }}
          return $this->render('user/client/clientUpdateDash.html.twig', [
            'controller_name' => 'UserController',
            'data'=>"",
            'form' => $form->createView()
        ]);
    }
  
    #[Route('/dashboard/coach/update/{id}', name: 'UpdateCoachDashboard')]
    public function UpdateCoachDashboard($id, ManagerRegistry $doctrine, Request  $request): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($form->isValid()){
             $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('listecoachs');
        }else{
            $data = "Failed to Update Coach";
            $response = new JsonResponse($data);
            return $this->render('user/coach/coachUpdateDash.html.twig', [
                'controller_name' => 'UserController',
                'data' => $response->getContent(),
                'form' => $form->createView()
            ]);
          }}
          return $this->render('user/coach/coachUpdateDash.html.twig', [
            'controller_name' => 'UserController',
            'data'=>"",
            'form' => $form->createView()
        ]);
    }
  
    #[Route('/dashboard/doctor/update/{id}', name: 'UpdateDoctorDashboard')]
    public function UpdateDoctorDashboard($id, ManagerRegistry $doctrine, Request  $request): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($form->isValid()){ 
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('listeDoctor');
        }else{
            $data = "Failed to Update Doctor";
            $response = new JsonResponse($data);
            return $this->render('user/doctor/doctorUpdateDash.html.twig', [
                'controller_name' => 'UserController',
                'data' => $response->getContent(),
                'form' => $form->createView()
            ]);
          }}
          return $this->render('user/doctor/doctorUpdateDash.html.twig', [
            'controller_name' => 'UserController',
            'data'=>"",
            'form' => $form->createView()
        ]);
    }
     
    #[Route('/dashboard/pharmacien/update/{id}', name: 'UpdatePharmacienDashboard')]
    public function UpdatePharmacienDashboard($id, ManagerRegistry $doctrine, Request  $request): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($form->isValid()){ 
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('listePharmaciens');
        }else{
            $data = "Failed to Update Pharmacien";
            $response = new JsonResponse($data);
            return $this->render('user/pharmacien/pharmacienUpdateDash.html.twig', [
                'controller_name' => 'UserController',
                'data' => $response->getContent(),
                'form' => $form->createView()
            ]);
          }}
          return $this->render('user/pharmacien/pharmacienUpdateDash.html.twig', [
            'controller_name' => 'UserController',
            'data'=>"",
            'form' => $form->createView()
        ]);
    }   
}
