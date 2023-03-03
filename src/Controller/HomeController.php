<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
class HomeController extends AbstractController
{
    #[Route('/publish', name: 'publish')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'messageRecus'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
    
    #[Route('/updatenotification', name: 'updatenotification')]
    public function updatenotification(NotificationRepository $notificationRepository) 
    {   
       $user = $this->getUser(); 
       $notifications = $notificationRepository->findby(array('toUser'=> $user)); 
       foreach ($notifications as $notification) {
        $notification->setSeen(true) ; 
        $notificationRepository->save($notification) ;
        }
        return $this->redirectToRoute('homepageVisitor');

    }

    #[Route('/', name: 'homepageVisitor')]
    public function homepageVisitor(ProduitRepository $Rep, NotificationRepository $notificationRepository): Response
    {   
        $produits = $Rep->findAll();
        if($this->getUser()){
            $user = $this->getUser() ; 
                  $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));            return $this->render('home/home.html.twig', [
                'notifications' => $notifications,
                'controller_name' => 'HomeController',
                'user' => $this->getUser(),
                'produits' => $produits 
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
   
    #[Route('/facture', name: 'facture')]
    public function pharmacien(): Response
    {
        return $this->render('/facture.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/error', name: 'app_error')]
    public function error(): Response
    { 
        return $this->render('error-404.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
