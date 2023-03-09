<?php

namespace App\Controller;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
