<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(CategoryRepository $categoryRepository, NormalizerInterface $normalizer): Response
    {
        $categorys = $categoryRepository->findAll();
        $CategoryNormalizer = $normalizer->normalize($categorys, 'json', ['groups' => "categorys"]);
        $json = json_encode($CategoryNormalizer);

        return new Response($json);
    }

    ////////////////////////////////////////-------------------------------------
    #[Route('/listesubservice', name: 'listesubservice')]
    public function listesubscription(SubscriptionRepository $subscriptionRepository, NormalizerInterface $normalizer): Response
    {
        $sub = $subscriptionRepository->findAll();
        $SubNormilizer = $normalizer->normalize($sub, 'json', ['groups' => "subscribers"]);
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
        $html = $twig->render('email/confirmeCompte.html.twig', [
            'user' => 'yessine',
            'message' => 'Please click the following link to reset your password: <a href="#">Reset Password</a>',
        ]);

        $email = (new Email())
            ->from('contact.fithealth23@gmail.com')
            ->to('haelkyll@gmail.com')
            ->subject('Order Confirmation')
            ->html($html);
        $transport = new GmailSmtpTransport('contact.fithealth23@gmail.com', 'qavkrnciihzjmtkp');
        $mailer = new Mailer($transport);
        $mailer->send($email);
        dd('done');
    }


    /*************************************Yessine SERVICES  */


    #[Route('/listecurrentsubservice', name: 'CurrentSub')]
    public function CurrentSub(UserRepository $userRepository, Request $request,  NormalizerInterface $normalizer): Response
    {
        $id = $request->query->get("id");
        $user = $userRepository->find($id);
        $subscription =  $user->getSubscriptions();
        $SubNormilizer = $normalizer->normalize($subscription, 'json', ['groups' => "subscribers"]);
        return new JsonResponse($SubNormilizer);
    }
    #[Route('/addsubscriptionclient', name: 'addSubClient')]
    public function addSubClient(
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository,
        Request $request,
        NormalizerInterface $normalizer
    ): Response {
        $id = $request->query->get("id");
        $user = $userRepository->find($id);
        $dateSub = $request->query->get("dateSub");
        $type = $request->query->get("type");
        $paiementType = $request->query->get("paiementType");
        $amount = $request->query->get("amount");

        $subscription = new Subscription();
        $subscription->setDateSub(new \DateTime());
        $subscription->setDateExpire(new \DateTime());
        $subscription->setType($type);
        $subscription->setPaiementType($paiementType);
        $subscription->setAmount((int)$amount);
        $subscription->setUser($user);
        $subscription->setState("confirmed");
        $subscription->setReference("referenceCODENAME12");

    $subscriptionRepository->save($subscription);


    
        $subscription =  $user->getSubscriptions();
        $SubNormilizer = $normalizer->normalize($subscription, 'json', ['groups' => "subscribers"]);
        return new JsonResponse($SubNormilizer,200);
    }

    #[Route('/updatesubstate', name: 'updateSubState')]
    public function updateSubState(
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository,
        Request $request,
        NormalizerInterface $normalizer
    ): Response {
        $id = $request->query->get("id");
        $state = $request->query->get("state");
        $subscription = $subscriptionRepository->find($id);
        $subscription->setState($state);
        $subscriptionRepository->save($subscription);


        return new JsonResponse("subscription updated with success", 200);
    }

    #[Route('/deletesub', name: 'deletesub')]
    public function deletesub(
        SubscriptionRepository $subscriptionRepository,
        Request $request
    ): Response {
        $id = $request->query->get("id");
        $subscription = $subscriptionRepository->find($id);
        $subscriptionRepository->remove($subscription);
        return new Response("subscription deleted with success", 200);
    }

    #[Route('/orderHistoryservice', name: 'orderHistoryservice')]
    public function orderHistoryservice(UserRepository $userRepository, Request $request, NormalizerInterface $normalizer): Response
    {
        $id = $request->query->get("id");
        $user = $userRepository->find($id);
        $order = $user->getOrders();
        $SubNormilizer = $normalizer->normalize($order, 'json', ['groups' => "order"]);
       // $json = json_encode($SubNormilizer);
        return new JsonResponse($SubNormilizer);
    }

  

}
