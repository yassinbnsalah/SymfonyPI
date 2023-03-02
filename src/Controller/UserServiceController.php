<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserServiceController extends AbstractController
{
    

    #[Route("user/signup", name:"app_service_register")]
  
    public function signupAction(Request $request)
    {
        $CIN = $request->query->get("CIN");
        $Name = $request->query->get("Name");
        $Email = $request->query->get("Email");
        $Adresse = $request->query->get("Adresse");
        $Password = $request->query->get("Password");
      

        if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
            return new Response("email invalid.");
        }
        $user = new User();
        $user->setCIN($CIN);
        $user->setName($Name);
        $user->setEmail($Email);
        $user->setAdresse($Adresse);
        $user->setPassword($Password);
        try {
            $em = $this->getDoctrine()->getManager();
            $em ->persist($user);
            $em -> flush();

            return new JsonResponse("Account is cretaed", 200);
        }catch(\Exception $ex) {
            return new Response("exception".$ex->getMessage());
        }
    }


    


     #[Route("user/signin", name:"app_service_login")]
     
    public function siginAction(Request $request) 
    {
        $Email = $request->query->get("Email");
        $Password = $request->query->get("Password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['Email'=>$Email]);

        if($user) {
            if($Password == $user->getPassword()) {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else {
                return new Response("password not found");
            }
        }
        else 
        {
            return new Response("user not found");
        }
    }
}
