<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(): Response
    {
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);
    }
    #[Route('/notification/history/client', name: 'notificationHistoryClient')]
    public function notificationHistoryClient(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser() ; 
        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
        
        return $this->render('notification/notificationHistoryClient.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications,
            'user' => $user 
        ]);
    }



    #[Route('/notification/history/admin', name: 'notificationhistoryadmin')]
    public function notificationhistoryadmin(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findBy(array(), array('dateNotification' => 'DESC'));
        $user = $this->getUser() ; 
        return $this->render('notification/notificationHistory.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications,
            'user' => $user 
        ]);
    }

    #[Route('/notification/delete/{id}', name: 'DeleteNotification')]
    public function DeleteNotification($id , NotificationRepository $notificationRepository): Response
    {
        $notification = $notificationRepository->find($id);
        $notificationRepository->remove($notification); 
        $user = $this->getUser() ; 
       return $this->redirectToRoute('notificationhistoryadmin');
    }
}
