<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use App\Entity\Session;
use App\Repository\SeanceRepository;
use App\Service\MailerService;
use MailerService as GlobalMailerService;
use MailerServices;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Session\Session as SessionSession;

class SportController extends AbstractController
{
    #[Route('/sport', name: 'app_sport')]
    public function index(): Response
    {
        return $this->render('sport/index.html.twig', [
            'controller_name' => 'SportController',
        ]);
    }
    #[Route('/dashboard/coach/activity', name: 'activityListe')]
    public function activityListe(ActivityRepository $repo,Request $req,ManagerRegistry $em): Response
    {
        $data = null ; 
        $user = $this->getUser();
        $act = $repo->findAll();
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class,$activity);
        $form->handleRequest($req);
        if($form->isSubmitted())
        {
            if($form->isValid()){
                $em = $em->getManager(); 
                $em->persist($activity);
                $em->flush() ;
                return $this->redirectToRoute('activityListe'); 
            }
            else{
                $data = "can not add this activity check " ;
                return $this->render('user/coach/activityList.html.twig', [
                   'data' => $data ,
                    'user' => $user,
                    'activites' => $act,
                    'form' => $form->createView()
                ]);
            }
        
        }
        return $this->render('user/coach/activityList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user,
            'activites' => $act,
            'data' => $data , 
            'form' => $form->createView()
        ]);
    }

    
    // #[Route('/dashboard/coach/seances', name: 'seanceListe')]
    // public function seanceListe(): Response
    // {
    //     $user = $this->getUser();
    //     return $this->render('user/coach/seanceList.html.twig', [
    //         'controller_name' => 'SportController',
    //         'user' => $user
    //     ]);
    // }

     
    #[Route('/dashboard/coach/planning', name: 'planningListe')]
    public function planningListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/planningList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

    #[Route('/dashboard/coach/client', name: 'CoachclientList')]
    public function CoachclientList(UserRepository $userRepository): Response
    {   
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = $this->getUser();
        return $this->render('user/coach/listClient.html.twig', [
            'controller_name' => 'SportController',
            'User_client' => $User_client,
            'user' => $user
        ]);
    }



    #[Route('/dashboard/coach/activity/update/{id}', name: 'Update_Activity')]
    public function UpdateActivity($id , Request $req,ManagerRegistry $em, ActivityRepository $repo): Response
    {
        $activity = $repo->find($id) ; 
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($req) ; 
        $data = null ;
        $user = $this->getUser();
        if($form->isSubmitted())
        {
            if($form->isValid()){
                $em = $em->getManager(); 
                $em->persist($activity);
                $em->flush() ;
                return $this->redirectToRoute('activityListe'); 
            } 
            else{
                $data = "can not update this activity check " ;
                return $this->render('user/coach/updateActivity.html.twig', [
                   'data' => $data ,
                    'user' => $user,
                    'activites' => $activity,
                    'form' => $form->createView()
                ]);
            }
        
        }
        return $this->render('user/coach/updateActivity.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user,
            'data' => $data , 
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/coach/disponibility/delete/{id}', name: 'DeleteActivity')]
    public function DeleteActivity($id ,ManagerRegistry $em, ActivityRepository $repo): Response
    {
            $activity = $repo->find($id) ; 
            $em = $em->getManager(); 
            $em->remove($activity);
            $em->flush() ; 
            return $this->redirectToRoute('activityListe'); 
       
    }

//     #[Route('/dashboard/coach/activity', name: 'findActivity')]
//     public function rechercherActivite(ActivityRepository $actRepo,Request $request)
// {
//     $form = $this->createFormBuilder()
//         ->add('find', TextType::class)
//         ->add('Rechercher', SubmitType::class, ['label' => 'Rechercher'])
//         ->getForm();

//         $form->handleRequest($request);
    
//     if ($form->isSubmitted() && $form->isValid()) {
//         $search = $form->getData()['find'];
//         // Recherche dans la base de données en utilisant la valeur saisie
//         $results = $actRepo
//             ->getRepository(Activity::class)
//             ->findBy(['name' => $search]);
        
//         return $this->render('user/coach/activityList.html.twig', [
//             $user = $this->getUser(),
//             'form' => $form->createView(),
//             'results' => $results
//         ]);
//     }
    
//     return $this->render('user/coach/activityList.html.twig', [
//         $user = $this->getUser(),
//         'form' => $form->createView()
//     ]);
// }
        // public function findByName($name)
        // {
        //     return $this->createQueryBuilder('a')
        //         ->where('a.name LIKE :name')
        //         ->setParameter('name', '%'.$name.'%')
        //         ->orderBy('a.name', 'ASC')
        //         ->getQuery()
        //         ->getResult();
        // }
    
        #[Route('/dashboard/coach/activity', name: 'findActivity')]
        public function findActivity(ActivityRepository $activityRepository, Request $request)
        {
            $user = $this->getUser();
            $searchForm = $this->createFormBuilder()
                ->add('search', TextType::class, ['required' => false])
                ->add('submit', SubmitType::class, ['label' => 'Search'])
                ->getForm();

            $searchForm->handleRequest($request);

            if ($searchForm->isSubmitted() && $searchForm->isValid()) {
                $name = $searchForm->get('search')->getData();
                $activities = $activityRepository->findByName($name);
            } else {
                $activities = $activityRepository->findAll();
            }

            return $this->render('user/coach/activityList.html.twig', [
                'controller_name' => 'SportController',
                'user' => $user,
                'activities' => $activities,
                'searchForm' => $searchForm->createView()
            ]);
        }

        

        #[Route('/dashboard/coach/activity', name: 'emailing')]
                public function sendEmail(MailerServices $mailerService): Response
        {
            $recipientEmail = 'example@example.com';
            $subject = 'Annulation de séance';
            $template = 'emails/cancel_seance.html.twig';
            $from = 'fitHealth@gmail.com';
            

            // $mailerService->sendEmail($subject, );

            return new Response('Email sent successfully');
        }
}