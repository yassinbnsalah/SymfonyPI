<?php

namespace App\Controller;
### yessine
use DateInterval;
use App\Entity\User;
use App\Form\addType;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Form\ProfileType;
use App\Entity\Subscription;
use App\Entity\Disponibility;
use App\Entity\Notification;
use App\Form\SubscriptionType;
use App\Form\DisponibilityType;
use App\Manager\RealTimeManager;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;

use App\Repository\OrdennanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SubscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserController extends AbstractController
{
    #[Route('/admin', name: 'admindash')]
    public function index(UserRepository $userRepository,SubscriptionRepository $subscriptionRepository)
    {   $env = $_ENV['APP_ENV'] ; 
        $User_admin = $userRepository->findByRole('["ROLE_ADMIN"]');
        $countSubscriptions = $subscriptionRepository->countSubscriptions();
        $Count_admin = $userRepository->countUserByRole('ROLE_ADMIN');
        $Count_medcin = $userRepository->countUserByRole('ROLE_MEDCIN');
        $Count_client = $userRepository->countUserByRole('ROLE_CLIENT');
        $Count_coach = $userRepository->countUserByRole('ROLE_COACH');
        $countSubscribers = $subscriptionRepository->countSubscribers();
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'UserController',
            'User_admin' => $User_admin,
            'Count_admin' => $Count_admin,
            'Count_medcin' => $Count_medcin,
            'Count_client' => $Count_client,
            'Count_coach' => $Count_coach,
            'countSubscribers' => $countSubscribers,
            'countSubscriptions' => $countSubscriptions,
            'env' => $env
        ]);
    }
    #[Route('/allticket', name: 'allticket')]
    public function allticket(TicketRepository $repo,PaginatorInterface $paginator,Request $request): Response
    {
        $allticket = $repo->findAll();
        $tickets = $paginator->paginate(
            $allticket, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        return $this->render('user/client/listticket.html.twig', [
            'controller_name' => 'UserController',
            'allticket' => $tickets
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
    public function showCoach(UserRepository $userRepository,
    NotificationRepository $notificationRepository
)
    {
        $User_coach = $userRepository->findByRole('["ROLE_COACH"]');
        $user = $this->getUser();
              $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));        return $this->render('user/coach/showCoach.html.twig', [
            'controller_name' => 'UserController',
            'User_coach' => $User_coach,
            'notifications' => $notifications,
            'user' => $user
        ]);
    }
    #[Route('/doctor/showDoctor', name: 'showDoctor')]
    public function showDoctor(UserRepository $userRepository,NotificationRepository $notificationRepository)
    {
        $User_doctor = $userRepository->findByRole('["ROLE_MEDCIN"]');
        $user = $this->getUser();
              $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));        return $this->render('user/doctor/showDoctor.html.twig', [
            'controller_name' => 'UserController',
            'User_doctor' => $User_doctor,
            'notifications' => $notifications,
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
    public function listeClient(UserRepository $userRepository,PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager)
    {
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        $clients = $paginator->paginate(
            $User_client, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
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
                    'data' => $response->getContent(),
                    'User_client' => $clients,
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/client/listeClient.html.twig', [
            'controller_name' => 'UserController',
            'data' => "",
            'User_client' => $clients,
            'form' => $form->createView()
        ]);
    }



    #[Route('/client/listesub', name: 'listeSubClient')]
    public function listeSubClient(NotificationRepository $notificationRepository
    ): Response
    {
        $user = $this->getUser();
              $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
        
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

		/** @var User $user */
		$user = $this->getUser();
        if($user->getRoles()[0] == 'ROLE_CLIENT'){
        return match ($user->isVerified()) {
			true => $this->render('user/client/clientdashsub.html.twig', [
                'controller_name' => 'UserController',
                'user' => $user
            ]),
			false => $this->render("user/client/please-verify-email.html.twig"),
		};
    }
    else{
        return $this->render('user/client/clientdashsub.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'notifications' => $notifications

        ]);
    }

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
    public function UpdateClientData($id, ManagerRegistry $doctrine,Request  $request,
    NotificationRepository $notificationRepository): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
              $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));        if ($form->isSubmitted()) {
       
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
    'notifications' => $notifications,
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
    public function clientDetails(Request $req, $id, ManagerRegistry $em,
    NotificationRepository $notificationRepository,HubInterface $hub,
    NormalizerInterface $normalizer,RealTimeManager $realTimeManager): Response
    {
        $ClientByID = $em->getRepository(User::class)->find($id);
        $sb = new Subscription();
        $sb->setDateSub(new \DateTime());
        $date = new \DateTime();
        $sb->setReference("SUB-". strtoupper($ClientByID->getName()) .$date->format('-Y-m-d'));
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
                $notification = new Notification() ; 
                $notification->setDateNotification(new \DateTime()); 
                $notification->setMessage('you have approved new sub') ; 
                $notification->setToUser($ClientByID) ;
                $notification->setPath("sub") ;
                $notification->setSeen(false);
                $notificationRepository->save($notification); 
                $notificationJSON = $normalizer->normalize($notification  , 'json', ['groups' => "notification"]);
                $json = json_encode($notificationJSON);
                $realTimeManager->Walker($json,$hub);
               
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
    public function listeDoctor(UserRepository $userRepository,PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager)
    {
        $User_medcin = $userRepository->findByRole('["ROLE_MEDCIN"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        $doctors = $paginator->paginate(
            $User_medcin, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
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
                    'User_medcin' => $doctors,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
              }}
              return $this->render('user/doctor/listeDoctor.html.twig', [
                'controller_name' => 'UserController',
                'User_medcin' => $doctors,
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
                $dispo->setHeureStart(new \DateTime($req->request->get('HeureStart'))); 
                
                $dispo->setHeureEnd(new \DateTime($req->request->get('HeureEnd'))); 
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
    public function listePharmaciens(UserRepository $userRepository, PaginatorInterface $paginator,Request $request, EntityManagerInterface $manager)
    {
        $User_pharmacien = $userRepository->findByRole('["ROLE_PHARMACIEN"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        $pharmaciens = $paginator->paginate(
            $User_pharmacien, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
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
                    'User_pharmacien' => $pharmaciens,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
              }}
              return $this->render('user/pharmacien/listePharmacien.html.twig', [
                'controller_name' => 'UserController',
                'User_pharmacien' => $pharmaciens,
                'data'=>"",
                'form' => $form->createView()
            ]);
        }
   



    #[Route('/coach/liste', name: 'listecoachs')]
    public function listecoachs(UserRepository $userRepository,PaginatorInterface $paginator ,Request $request, EntityManagerInterface $manager)
    {
        $User_coach = $userRepository->findByRole('["ROLE_COACH"]');
        $user = new User();
        $form = $this->createForm(addType::class, $user);
        $form->handleRequest($request);
        $coachs = $paginator->paginate(
            $User_coach, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
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
                    'User_coach' => $coachs,
                    'data' => $response->getContent(),
                    'form' => $form->createView()
                ]);
              }}
              return $this->render('user/coach/listeCoach.html.twig', [
                'controller_name' => 'UserController',
                'data'=>"",
                'User_coach' => $coachs,
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
    #[Route('/dashboard/client/ticket', name: 'ticketAdd')]
    public function ticketAdd(Request $req,ManagerRegistry $em,NotificationRepository $notificationRepository): Response
    {
        $ticket = new Ticket() ; 
        $ticket->setDateTicket(new \DateTime()); 
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($req) ; 
        $user = $this->getUser();
        $data = null ; 
              $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));        if($form->isSubmitted()){
            if($form->isValid()){
            $ticket->setOwner($user);
            $ticket->setState("Pending");
            $em = $em->getManager(); 
            $em->persist($ticket);
            $em->flush() ; 
            return $this->redirectToRoute('ticketAdd');
        }
        else{
            $data = "Failed to add new Ticket ";
            // $response = new JsonResponse($data);
            return $this->render('user/client/clientticket.html.twig', [
                'controller_name' => 'UserController',
                'user' => $user,
                'data' =>  $data,
                'notifications' => $notifications,
                'form' => $form->createView()
            ]);
        }
        }
     
        return $this->render('user/client/clientticket.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'data' =>  $data,
            'notifications' => $notifications,
            'form' => $form->createView()
        ]);
    }

    #[Route('/updateVerif/{id}', name: 'UpdateUserVerif')]
    public function UpdateUserVerif($id, UserRepository $userRepo): Response
    {
        $user = $userRepo->find($id);
        $user->setIsVerified(true);
        $userRepo->save($user);
        return $this->redirectToRoute('listeSubClient');
    }


    #[Route('/ticket/delete/{id}', name: 'deleteTicket')]
    public function deleteTicket($id, TicketRepository $repo, ManagerRegistry $em): Response
    {
        $tickettodelete = $repo->find($id);
        $em = $em->getManager();
        $result = $em->remove($tickettodelete);
        $em->flush();
        // dd($allsub);
        return $this->redirectToRoute('allticket');
    }
 
    #[Route('/ticket/progress/{id}', name: 'progressTicket')]
    public function progressTicket($id, TicketRepository $repo, ManagerRegistry $em): Response
    {
        $progressTicket = $repo->find($id);
        $progressTicket->setState("In progress");
        $em = $em->getManager();
        $em->persist($progressTicket);
        $em->flush();
        return $this->redirectToRoute('allticket');
    }

    #[Route('/ticket/confirmed/{id}', name: 'ConfirmedTicket')]
    public function ConfirmedTicket($id, TicketRepository $repo, ManagerRegistry $em): Response
    {
        
        $ConfirmedTicket = $repo->find($id);
        $ConfirmedTicket->setState("Confirmed");
        $em = $em->getManager();
        $em->persist($ConfirmedTicket);
        $em->flush();
        return $this->redirectToRoute('allticket');
    }


   
}
