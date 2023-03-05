<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
        return new JsonResponse($userlogin,200);
    }

    #[Route("user/liste", name: "allclient")]

    public function allclient(UserRepository $userRepository, Request $request, NormalizerInterface $normalizer)
    {
       
        $user = $userRepository->findAll();
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
            if($user->getRoles()[0] == 'ROLE_ADMIN'){
                if ($Password == $user->getPassword()) {
                    $userlogin = $normalizer->normalize($user, 'json', ['groups' => "user"]);
                  //  $json = json_encode($userlogin);
                    return new JsonResponse($userlogin);
                } else {
                    return new JsonResponse("password not found", 500);
                }
            }else
            {
                return new JsonResponse("No Admin founded", 300);
            }
          
        } else {
            return new JsonResponse("user not found", 400);
        }
    }
    
     #[Route("user/deleteUser", name:"delete_User")]
     #[Method("DELETE")]
    
     public function deletePostAction(Request $request) {

        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $Post = $em->getRepository(User::class)->find($id);
        if($Post!=null ) {
            $em->remove($Post);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("utilisateur a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Post invalide.");


    }


     #[Route("user/editUser", name:"app_gestion_profile")]
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
            ));

            try {
                $em = $this->getDoctrine()->getManager();
                $em -> persist($user);
                $em -> flush();
    
                return new JsonResponse("success", 200);
            }catch(\Exception $ex) {
                return new Response("failed".$ex->getMessage());
            }

        
    }
}
