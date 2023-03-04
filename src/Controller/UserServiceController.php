<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserServiceController extends AbstractController
{


    #[Route("user/signup", name: "app_service_register")]

    public function signupAction(Request $request)
    {
        $CIN = $request->query->get("CIN");
        $Name = $request->query->get("Name");
        $Email = $request->query->get("Email");
        $Adresse = $request->query->get("Adresse");
        $Password = $request->query->get("Password");
        $Numero = $request->query->get("Numero");
        $age = $request->query->get("age");

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
        $user->setAge($age);
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
        return new JsonResponse($userlogin,200);
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
                $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
                $json = json_encode($userlogin);
                return new JsonResponse($json);
            } else {
                return new Response("password not found", 500);
            }
        } else {
            return new Response("user not found", 400);
        }
    }
}
