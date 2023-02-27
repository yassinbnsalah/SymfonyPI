<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use App\Repository\CategoryRepository;
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

    #[Route('/sendmail', name: 'sendmail')]
    public function sendmail()
    {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader);
        $html = $twig->render('email/test.html.twig', [
          'user' => 'yessine',
          'message' => 'Please click the following link to reset your password: <a href="#">Reset Password</a>',
      ]);
      
        $name ="yessine" ;
        $username="yessine" ;
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
}
