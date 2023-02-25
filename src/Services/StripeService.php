<?php

namespace App\Services ;

use App\Entity\Order;

class StripeService{
    private $privateKey ; 
    public function __construct()
    {
        // verify if project which mode is it ? dev or prod 
        if($_ENV['APP_ENV'] === 'dev' ){
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'] ; 
        }else{
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'] ; 
        }
    }

    
    /**
     * @param Order $order
     * @return \Stripe\PaymentIntent
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function paymentIntent(Order $order){
        \Stripe\Stripe::setApiKey($this->privateKey);

        return \Stripe\PaymentIntent::create([
            'amount' => $order->getPrice() * 100,
            'currency' => 'USD',
            'payment_method_types' => ['card']
        ]);
    }

    public function paiement(
        $amount,
        $currency,
        $description,
        array $stripeParameter
    )
    {
        \Stripe\Stripe::setApiKey($this->privateKey);
        $payment_intent = null;

        if(isset($stripeParameter['stripeIntentId'])) {
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
            
        }
       
        if($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            //dd("succes"); 
        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

     /**
     * @param array $stripeParameter
     * @param Order $order
     * @return \Stripe\PaymentIntent|null
     */
    public function stripe(array $stripeParameter, Order $order)
    {
        return $this->paiement(
            $order->getPrice() * 100,
            'USD',
            $order->getReference(),
            $stripeParameter
        );
    }
}
