<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function activityListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/activityList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

    
    #[Route('/dashboard/coach/seances', name: 'seanceListe')]
    public function seanceListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/seanceList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

     
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
    public function CoachclientList(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/listClient.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

    #[Route('/dashboard/coach/Activity/add', name: 'add_activity')]
    public function addActivity(Request $req,ManagerRegistry $em): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class,$activity);
        $form->handleRequest($req);
        $nom = $activity->getNom();
        $description = $activity->getDescription();
        $img = $activity->getImage();

        if($form->isSubmitted())
        {
            $activity->setNom($nom);
            $activity->setDescription($description);
            $activity->setImage($img);
            $em = $em->getManager(); 
            $em->persist($activity);
            $em->flush() ;
        }
            return $this->render('user/coach/activityList.html.twig',
        [
            'controller_name' => 'SportController',
            'nom' => $nom,
            'description' => $description,
            'image' => $img,
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/coach/Activity/update/{id}', name: 'Update_Activity')]
    public function UpdateActivity($id , Request $req,ManagerRegistry $em, ActivityRepository $repo): Response
    {
        $activity = $repo->find($id) ; 
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($req) ; 

        $nom = $activity->getNom();
        $description = $activity->getDescription();
        $img = $activity->getImage();

        if($form->isSubmitted()){
            $activity->setNom($nom);
            $activity->setDescription($description);
            $activity->setImage($img);
            $em = $em->getManager(); 
            $em->persist($activity);
            $em->flush() ; 
            return $this->redirectToRoute('addActivity'); 
        }
        return $this->render('user/doctor/updateDisponibility.html.twig', [
            'controller_name' => 'SportController',
            'nom' => $nom,
            'description' => $description,
            'image' => $img,
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/coach/disponibility/delete/{id}', name: 'DeleteActivity')]
    public function DeleteActivity($id , Request $req,ManagerRegistry $em, ActivityRepository $repo): Response
    {
            $activity = $repo->find($id) ; 
            $nom = $activity->getNom();
            $description = $activity->getDescription();
            $img = $activity->getImage();

            $em = $em->getManager(); 
            $em->remove($activity);
            $em->flush() ; 
            $userConnected = $this->getUser(); 

            if ($userConnected->getRoles()[0] == 'ROLE_ADMIN'){
                return $this->redirectToRoute('addActivity', array('id' => $activity->getId())); 
            }
            return $this->redirectToRoute('addActivity'); 
       
    }
}