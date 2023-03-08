<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Form\SeanceType;
use App\Repository\NotificationRepository;
use App\Repository\SeanceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeanceController extends AbstractController
{
    #[Route('/seance', name: 'app_seance')]
    public function index(): Response
    {
        return $this->render('seance/index.html.twig', [
            'controller_name' => 'SeanceController',
        ]);
    }

    #[Route('/dashboard/coach/seance', name: 'listeSeance')]
    public function seanceListe(SeanceRepository $repo,Request $req,ManagerRegistry $em,
    NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
        $seances = $repo->findAll();
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class,$seance);
        $form->handleRequest($req);
        if($form->isSubmitted())
        {
            $em = $em->getManager(); 
            $em->persist($seance);
            $em->flush() ;
            return $this->redirectToRoute('listeSeance'); 
        }
        return $this->render('user/coach/seanceList.html.twig', [
            'controller_name' => 'SeanceController',
            'user' => $user,
            'seances' => $seances,
            'notifications' => $notifications,
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/coach/seance/update/{id}', name: 'Update_Seance')]
    public function UpdateSeance($id , Request $req,ManagerRegistry $em,
    NotificationRepository $notificationRepository , SeanceRepository $repo): Response
    {
        $seance = $repo->find($id) ; 
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($req) ; 

       
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
        if($form->isSubmitted()){
          
            $em = $em->getManager(); 
            $em->persist($seance);
            $em->flush() ; 
            return $this->redirectToRoute('listeSeance'); 
        }
        return $this->render('user/coach/updateSeance.html.twig', [
            'controller_name' => 'SeanceController',
            'user' => $user,
            'notifications' => $notifications,
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/coach/seance/delete/{id}', name: 'DeleteSeance')]
    public function DeleteActivity($id ,ManagerRegistry $em, SeanceRepository $repo): Response
    {
            $seance = $repo->find($id) ; 
            $em = $em->getManager(); 
            $em->remove($seance);
            $em->flush() ; 
            return $this->redirectToRoute('listeSeance');
    }
}
