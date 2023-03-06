<?php

namespace App\Controller;

use Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SmsController extends AbstractController
{
 
  /**
     * @Route("/send-sms", name="send_sms")
     */
    public function sendSms(Client $twilioClient): Response
    {
        // Replace with your own phone number and the phone number you want to send the SMS to
        $fromNumber = '+15673343714';
        $toNumber = '+21693293311';

        // Create the message
        $message = $twilioClient->messages->create(
            $toNumber, // The phone number to send the SMS to
            [
                'from' => $fromNumber, // The Twilio phone number to send the SMS from
                'body' => 'Hello, this is a test message from Twilio and amira bahloula!', // The message body
            ]
        );

        // Output the message SID
        echo 'SMS message sent with SID Amira ya bahloula: ' . $message->sid;

        return new Response();
    }
}