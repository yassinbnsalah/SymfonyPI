<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SportController extends AbstractController
{
    #[Route('/sport', name: 'app_sport')]
    public function index(): Response
    {
        return $this->render('sport/index.html.twig', [
            'controller_name' => 'SportController',
        ]);
    }
    #[Route('/dashboard/coach/activity', name: 'activityListe')]
    public function activityListe(
        ActivityRepository $repo,
        Request $req,
        ManagerRegistry $em,
        NotificationRepository $notificationRepository
    ): Response {
        $data = null;
        $user = $this->getUser();
        $act = $repo->findAll();
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($req);

        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $activity->setImage($newFilename);
                }
                $em = $em->getManager();
                $em->persist($activity);
                $em->flush();
                return $this->redirectToRoute('activityListe');
            } else {
                $data = "can not add this activity check ";
                return $this->render('user/coach/activityList.html.twig', [
                    'data' => $data,
                    'user' => $user,
                    'notifications' => $notifications,
                    'activites' => $act,
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/coach/activityList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user,
            'activites' => $act,
            'notifications' => $notifications,
            'data' => $data,
            'form' => $form->createView()
        ]);
    }


    // #[Route('/dashboard/coach/seances', name: 'seanceListe')]
    // public function seanceListe(): Response
    // {
    //     $user = $this->getUser();
    //     return $this->render('user/coach/seanceList.html.twig', [
    //         'controller_name' => 'SportController',
    //         'user' => $user
    //     ]);
    // }


    #[Route('/dashboard/coach/planning', name: 'planningListe')]
    public function planningListe(): Response
    {
        $user = $this->getUser();
        return $this->render('user/coach/planningList.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user
        ]);
    }

    #[Route('/dashboard/coach/client', name: 'CoachclientList')]
    public function CoachclientList(UserRepository $userRepository, NotificationRepository $notificationRepository): Response
    {
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = $this->getUser();

        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));


        return $this->render('user/coach/listClient.html.twig', [
            'controller_name' => 'SportController',
            'User_client' => $User_client,
            'user' => $user,
            'notifications' => $notifications
        ]);
    }



    #[Route('/dashboard/coach/activity/update/{id}', name: 'Update_Activity')]
    public function UpdateActivity(
        $id,
        Request $req,
        ManagerRegistry $em,
        ActivityRepository $repo,
        NotificationRepository $notificationRepository
    ): Response {
        $activity = $repo->find($id);
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($req);
        $data = null;
        $user = $this->getUser();

        $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $em->getManager();
                $em->persist($activity);
                $em->flush();
                return $this->redirectToRoute('activityListe');
            } else {
                $data = "can not update this activity check ";
                return $this->render('user/coach/updateActivity.html.twig', [
                    'data' => $data,
                    'user' => $user,
                    'notifications' => $notifications,
                    'activites' => $activity,
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/coach/updateActivity.html.twig', [
            'controller_name' => 'SportController',
            'user' => $user,
            'data' => $data,
            'notifications' => $notifications,
            'form' => $form->createView()
        ]);
    }

    #[Route('/dashboard/coach/disponibility/delete/{id}', name: 'DeleteActivity')]
    public function DeleteActivity($id, ManagerRegistry $em, ActivityRepository $repo): Response
    {
        $activity = $repo->find($id);
        $em = $em->getManager();
        $em->remove($activity);
        $em->flush();
        return $this->redirectToRoute('activityListe');
    }
}
