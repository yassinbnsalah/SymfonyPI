<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(CategoryRepository $categoryRepository , NormalizerInterface $normalizer): Response
    {
        $categorys = $categoryRepository->findAll(); 
        $CategoryNormalizer = $normalizer->normalize($categorys , 'json', ['groups' => "categorys"]);
        $json = json_encode($CategoryNormalizer);

        return new Response($json);
    }

////////////////////////////////////////-------------------------------------
    #[Route('/listesubservice', name: 'listesubservice')]
    public function listesubscription(SubscriptionRepository $subscriptionRepository , NormalizerInterface $normalizer): Response
    {
        $sub = $subscriptionRepository->findAll(); 
        $SubNormilizer = $normalizer->normalize($sub , 'json', ['groups' => "subscribers"]);
        $json = json_encode($SubNormilizer);

        return new Response($json);
    }



    // #[Route('/testsommeDates' , name:'testAddDates')]

    // public function testAddDates(SubscriptionRepository $subscriptionRepository , NormalizerInterface $normalizer): Response
    // {
    //     $sub = $subscriptionRepository->findAll(); 
    //     $SubNormilizer = $normalizer->normalize($sub , 'json', ['groups' => "subscribers"]);
    //     $json = json_encode($SubNormilizer);
    //     $now   = time();
    //     $date2 = strtotime('2023-03-05');
    //     $diff  = abs($now - $date2);
    //     $retour = array();
 
    //     $tmp = $diff;
    //     $retour['second'] = (int)$tmp % 60;
     
    //     $tmp = floor( ($tmp - $retour['second']) /60 );
    //     $retour['minute'] = (int)$tmp % 60;
     
    //     $tmp = floor( ($tmp - $retour['minute'])/60 );
    //     $retour['hour'] = (int)$tmp % 24;
     
    //     $tmp = floor( ($tmp - $retour['hour'])  /24 );
    //     $retour['day'] = (int)$tmp;
    //     dd($retour) ; 
    //     return new Response($json);
    // }
/////////////////////////////////////-------------------------------------------
    #[Route('/sendmail', name: 'sendmail')]
    public function sendmail()
    {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader);
        $html = $twig->render('email/test.html.twig', [
          'user' => 'yessine',
          'message' => 'Please click the following link to reset your password: <a href="#">Reset Password</a>',
      ]);
      
        
        $email = (new Email())
        ->from('contact.fithealth23@gmail.com')
        ->to('yacinbnsalh@gmail.com')
        ->subject('Order Confirmation')
        ->html($html);
        $transport = new GmailSmtpTransport('contact.fithealth23@gmail.com','qavkrnciihzjmtkp');
        $mailer = new Mailer($transport);
        $mailer->send($email);
        dd('done');  
    }


    /*************************************Yessine SERVICES  */


    #[Route('/listecurrentsubservice', name: 'CurrentSub')]
    public function CurrentSub( NormalizerInterface $normalizer): Response
    {
        $user = $this->getUser();
        $subscription =  $user->getSubscriptions() ; 
       // $sub = $subscriptionRepository->findAll(); 
        $SubNormilizer = $normalizer->normalize($subscription  , 'json', ['groups' => "subscribers"]);
        $json = json_encode($SubNormilizer);

        return new Response($json);
    }
    #[Route('/orderHistoryservice', name: 'orderHistoryservice')]
    public function orderHistoryservice(NormalizerInterface $normalizer): Response
    {
        $user = $this->getUser(); 
        $order = $user->getOrders() ; 
        $SubNormilizer = $normalizer->normalize($order , 'json', ['groups' => "order"]);
        $json = json_encode($SubNormilizer);
        return new Response($json);
    }
    
}
