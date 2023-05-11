<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Manager\RealTimeManager;
use App\Repository\NotificationRepository;
use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderServicesController extends AbstractController
{
    #[Route('/order/services', name: 'app_order_services')]
    public function index(): Response
    {
        return $this->render('order_services/index.html.twig', [
            'controller_name' => 'OrderServicesController',
        ]);
    }
    /***************** */

    #[Route('/displayOrderClient', name: 'displayOrderClient')]
    public function displayOrderClient(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        OrderRepository $orderRepository
    ): Response {
        $id = $request->query->get("id");
        $user = $userRepository->find($id);
        $order = $orderRepository->findBy(array("client" => $user));

        $SubNormilizer = $normalizer->normalize($order, 'json', ['groups' => "order"]);
        return new JsonResponse($SubNormilizer);
    }


    #[Route('/displayOrder', name: 'displayOrder')]
    public function displayOrder(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        OrderRepository $orderRepository
    ): Response {
        $order = $orderRepository->findAll();
        $SubNormilizer = $normalizer->normalize($order, 'json', ['groups' => "order"]);
        return new JsonResponse($SubNormilizer);
    }

    #[Route('/orderDetails', name: 'orderDetails')]
    public function orderDetails(
        Request $request,
        NormalizerInterface $normalizer,
        OrderRepository $orderRepository,
        OrderLineRepository $orderLineRepository
    ): Response {
        $id = $request->query->get("id");
        $order = $orderRepository->find($id);
        $ORLDNormilizer = $normalizer->normalize($order, 'json', ['groups' => "order"]);
        return new JsonResponse($ORLDNormilizer);
    }

    #[Route('/orderligneDetails', name: 'orderligneDetails')]
    public function orderligneDetails(
        Request $request,
        NormalizerInterface $normalizer,
        OrderRepository $orderRepository,
        OrderLineRepository $orderLineRepository
    ): Response {
        $id = $request->query->get("id");
        $order = $orderRepository->find($id);
        $orderlignes = $orderLineRepository->findBy(array('relatedOrder' => $order));
        $ORLDNormilizer = $normalizer->normalize($orderlignes, 'json', ['groups' => "orderlignes"]);
        return new JsonResponse($ORLDNormilizer);
    }


    #[Route('/updateorderstate', name: 'updateorderstate')]
    public function Updateorderstate(
        Request $request,
        NormalizerInterface $normalizer,
        OrderRepository $orderRepo ,
        HubInterface $hub,
        NotificationRepository $notificationRepository,
        RealTimeManager $realTimeManager
    ): Response {
        $id = $request->query->get("id");
        $order = $orderRepo->find($id);
      
        $state = $request->query->get("state");
       
        if($state == '1'){
            $message = "Shipped" ;
            $order->setState("Shipped");
        }else if ($state == '2'){
            $message = "Confirmed" ;
            $order->setState("Confirmed");
        }else if ($state == '3'){
            $message = "Cancel" ;
            $order->setState("Cancel");
        }else if ($state == '4'){
            $message = "ready To Ship" ;
            $order->setState("ready To Ship");
        }
        $orderRepo->save($order);
        $SubNormilizer = $normalizer->normalize($order, 'json', ['groups' => "order"]);
        $notification = new Notification();
        $notification->setDateNotification(new \DateTime());
        $notification->setMessage('your order state has been updated to ' . $message);
        $notification->setToUser($order->getClient());
        $notification->setPath("order");
        $notification->setSeen(false);
        $notificationRepository->save($notification);

        $notificationJSON = $normalizer->normalize($notification, 'json', ['groups' => "notification"]);
        $json = json_encode($notificationJSON);
        $realTimeManager->Walker($json, $hub);
        return new JsonResponse($SubNormilizer);
        
    }
}
