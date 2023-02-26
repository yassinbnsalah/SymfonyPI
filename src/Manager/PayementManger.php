<?php

namespace App\Manager;

use App\Entity\Order;

use App\Entity\User;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PayementManger
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var StripeService
     */
    protected $stripeService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StripeService $stripeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StripeService $stripeService
    ) {
        $this->em = $entityManager;
        $this->stripeService = $stripeService;
    }

    public function intentSecret(Order $order)
    {
        $intent = $this->stripeService->paymentIntent($order);
        
        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameter
     * @param Order $order
     * @return array|null
     */
    public function stripe(array $stripeParameter, Order $order)
    {
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $order);
        
        if($data) {
            // $resource = [
            //     'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
            //     'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
            //     'stripeId' => $data['charges']['data'][0]['id'],
            //     'stripeStatus' => $data['charges']['data'][0]['status'],
            //     'stripeToken' => $data['client_secret']
            // ];
        }

        return $resource;
    }

   
}