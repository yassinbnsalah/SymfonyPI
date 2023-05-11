<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ticket;
use Twilio\Rest\Client;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServiceController extends AbstractController
{

    #[Route("/user/signup", name: "app_service_register")]
    public function signupAction(Request $request)
    {
        $CIN = $request->query->get("CIN");
        $Name = $request->query->get("Name");
        $Email = $request->query->get("Email");
        $Adresse = $request->query->get("Adresse");
        $Password = $request->query->get("Password");
        $Numero = $request->query->get("Numero");
        $age = $request->query->get("Age");

        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid.", 400);
        }

        $user = new User();
        $user->setCIN($CIN);
        $user->setName($Name);
        $user->setEmail($Email);
        $user->setAdresse($Adresse);
        $user->setPassword($Password);
        $user->setNumero($Numero);
        $user->setAge((int)$age);
        $user->setRoles(['ROLE_CLIENT']);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("Account is cretaed", 200);
        } catch (\Exception $ex) {
            return new Response("exception :" . $ex->getMessage());
        }
    }


    #[Route("user/details", name: "detailsCLient")]

    public function detailsCLient(UserRepository $userRepository, Request $request, NormalizerInterface $normalizer)
    {
        $id = $request->query->get("id");
        $user = $userRepository->find($id);
        $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
        //  $json = json_encode($userlogin);
        return new JsonResponse($userlogin, 200);
    }

    #[Route("user/liste", name: "allclient")]

    public function allclient(UserRepository $userRepository, Request $request, NormalizerInterface $normalizer)
    {

        $user = $userRepository->findAll();
        $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
        //  $json = json_encode($userlogin);
        return new JsonResponse($userlogin, 200);
    }



    #[Route("user/signin", name: "app_service_login")]

    public function siginAction(Request $request, NormalizerInterface $normalizer)
    {
        $Email = $request->query->get("Email");
        $Password = $request->query->get("Password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['Email' => $Email]);

        if ($user) {

            if ($Password == $user->getPassword()) {
                if ($user->getRoles()[0] == 'ROLE_ADMIN') {
                    $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
                } else {
                    $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
                }
                //  $json = json_encode($userlogin);
                return new JsonResponse($userlogin, 200);
            } else {
                return new JsonResponse("password not found", 500);
            }
        } else {
            return new JsonResponse("No User founded", 300);
        }
    }

    #[Route("user/deleteUser", name: "delete_User")]
    public function deletePostAction(Request $request)
    {

        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $Post = $em->getRepository(User::class)->find($id);
        if ($Post != null) {
            $em->remove($Post);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("utilisateur a ete supprimee avec success.");
            return new JsonResponse($formatted);
        }
        return new JsonResponse("id Post invalide.");
    }


    #[Route("user/editUser", name: "app_gestion_profile")]
    public function editUser(Request $request, UserPasswordEncoderInterface $PasswordEncoder)
    {
        $id = $request->query->get("id");
        $Name = $request->query->get("Name");
        //$Numero = $request->query->get("Numero");
        $Email = $request->query->get("Email");
        //$Adresse = $request->query->get("Adresse");
        $Password = $request->query->get("Password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        // if($request->files->get("photo") != null)
        // {
        //  $file = $request->files->get("photo");
        // $fileName = $file->getClientOriginalName();
        // $file->move(
        //   $fileName
        //  );
        // $user->setCIN($CIN);
        $user->setName($Name);
        //$user->setNumero($Numero);
        $user->setEmail($Email);
        // $user->setAdresse($Adresse);
        $user->setPassword(
            $PasswordEncoder->encodePassword(
                $user,
                $Password
            )
        );

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("success", 200);
        } catch (\Exception $ex) {
            return new Response("failed" . $ex->getMessage());
        }
    }


    #[Route("user/details", name: "details_User")]
    public function details_User(Request $request, UserRepository $userRepository, NormalizerInterface $normalizer)
    {
        $id = $request->get("id");
        $user = $userRepository->find($id);
        if ($user != null) {
            $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
            return new JsonResponse($userlogin, 200);
        }
        return new JsonResponse("id Post invalide.");
    }
    #[Route('/ticket/listTicket', name: 'listTicket')]
    public function listTicket(TicketRepository $ticketRepository, NormalizerInterface $normalizer): Response
    {
        $sub = $ticketRepository->findAll();
        $SubNormilizer = $normalizer->normalize($sub, 'json', ['groups' => "ticket"]);
        $json = json_encode($SubNormilizer);

        return new Response($json);
    }

    
    #[Route("/ticket/addTicket", name: "app_service_ajouterTicket")]
    public function ajouterTicket(Request $request,Client $twilioClient)
    {
        $titre = $request->query->get("titre");
        $message = $request->query->get("message");
        $owner_id = $request->query->get("owner_id");



        $ticket = new Ticket();
        $ticket->setDateTicket(new \DateTime());
        $ticket->setTitre($titre);
        $ticket->setMessage($message);
        $owner = $this->getDoctrine()->getRepository(User::class)->find($owner_id);
        $ticket->setOwner($owner);
        $ticket->setState("Pending");
 
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            $fromNumber = '+15673343714';
            $toNumber = '+21693293311';

            // Create the message
            $message = $twilioClient->messages->create(
                $toNumber, // The phone number to send the SMS to
                [
                    'from' => $fromNumber, // The Twilio phone number to send the SMS from
                    'body' => 'Hello, Your Ticket are saved!', // The message body
                ]
            );
            return new JsonResponse("Ticket is cretaed", 200);
        } catch (\Exception $ex) {
            return new Response("exception :" . $ex->getMessage());
        }
    }



}