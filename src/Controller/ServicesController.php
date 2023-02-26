<?php

namespace App\Controller;

use App\Repository\CategoryRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
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
        $email = (new Email())
            ->from('contact.fithealth23@gmail.com')
            ->to('yacinbnsalh@gmail.com')
            ->subject('firstmail')
            ->text('hello ma men'); 
            $transport = new GmailSmtpTransport('contact.fithealth23@gmail.com','qavkrnciihzjmtkp');
            $mailer = new Mailer($transport);
            $mailer->send($email); 
            dd('done'); 
    }
}
