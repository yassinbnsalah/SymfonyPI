<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
 
    #[Route('/registration', name: 'app_registration')]
    public function registration(Request $request, EntityManagerInterface $manager) {
    {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            /** @var UploadedFile $imageFile */
           
            $imageFile = $form->get('image')->getData();

           // if($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setImage($newFilename);

                $user->setRoles(['ROLE_CLIENT']);
               
                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();


                

                return $this->redirectToRoute('app_login');
          }

        
        return $this->render('registration/registration.html.twig',
    [
        'form' => $form->createView()
    ]);
    }
}
}