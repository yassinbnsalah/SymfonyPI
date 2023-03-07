<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Repository\SubscriptionRepository;
use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubController extends AbstractController
{
    #[Route('/subscribers', name: 'all_sub')]
    public function index(SubscriptionRepository $repo, PaginatorInterface $paginator,Request $request
    ,NotificationRepository $notificationRepository): Response
    {
        $data = $repo->findBy(array(), array('dateSub' => 'DESC'));
        $allsub = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12 // Nombre de résultats par page
        );
        $user = $this->getUser(); 
        $notifications = $notificationRepository->findBy(array(), array('dateNotification' => 'DESC'));

        return $this->render('sub/index.html.twig', [
            'controller_name' => 'SubController',
            'Subscriptions' => $allsub,
            'user' => $user ,
            'notifications' => $notifications,

        ]);
    }

    #[Route('/dashboard/client/subhistory', name: 'subhistory')]
    public function subhistory(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
              $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));        return $this->render('user/client/clientsubhistory.html.twig', [
            'controller_name' => 'SubController',
            'user' => $user,
            'notifications' => $notifications
        ]);
    }


    #[Route('/subscription/delete/{id}', name: 'deleteSub')]
    public function deleteSubscription($id, SubscriptionRepository $repo, ManagerRegistry $em): Response
    {
        $subtodelete = $repo->find($id);
        $user = $subtodelete->getUser();
        $em = $em->getManager();
        $result = $em->remove($subtodelete);
        $em->flush();
        // dd($allsub);
        return $this->redirectToRoute('clientDetails', array('id' => $user->getId()));
    }
 
    #[Route('/subscription/suspend/{id}', name: 'suspendSub')]
    public function suspandSubscription($id, SubscriptionRepository $repo, ManagerRegistry $em): Response
    {
        $subtosuspend = $repo->find($id);
        $subtosuspend->setState("Suspended");
        $subtosuspend->setDateExpire(new \DateTime());
        $em = $em->getManager();
        $em->persist($subtosuspend);
        $em->flush();
        return $this->redirectToRoute('clientDetails', array('id' => $subtosuspend->getUser()->getId()));
    }

    #[Route('/subscription/insuspend/{id}', name: 'insuspendSub')]
    public function insuspandSubscription($id, SubscriptionRepository $repo, ManagerRegistry $em): Response
    {
        
        $subtosuspend = $repo->find($id);
       


        
        $subtosuspend->setState("Confirmed");
        
        $subtosuspend->setDateExpire(new \DateTime());
        $em = $em->getManager();
        $em->persist($subtosuspend);
        $em->flush();
        return $this->redirectToRoute('clientDetails', array('id' => $subtosuspend->getUser()->getId()));
    }
    #[Route('/subscription/cancel/{id}', name: 'cancelSub')]
    public function CancelSubscription($id, SubscriptionRepository $repo, ManagerRegistry $em): Response
    {
        $subtosuspend = $repo->find($id);
        $subtosuspend->setState("Cancel");
        $subtosuspend->setDateExpire(new \DateTime());
        $em = $em->getManager();
        $em->persist($subtosuspend);
        $em->flush();
        $user = $this->getUser();
        if ($user->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('clientDetails', array('id' => $subtosuspend->getUser()->getId()));
        } else {
            return $this->redirectToRoute('listeSubClient');
        }
    }
}
