<?php
// src/Service/MailerService.php
use Twig\Environment;

class MailerServices
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail($subject, $from, $to, $templateName, $context)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->twig->render($templateName, $context),
                'text/html'
            );
        $this->mailer->send($message);
    }
}

