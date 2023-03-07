<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Medicament;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Entity\Disponibility;
use App\Entity\Notification;
use App\Form\AddMedicamentType;
use App\Form\DisponibilityType;
use App\Manager\RealTimeManager;
use App\Repository\UserRepository;
use App\Repository\MedicamentRepository;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DisponibilityRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SanteController extends AbstractController
{
    #[Route('/sante', name: 'app_sante')]
    public function index(): Response
    {
        return $this->render('sante/index.html.twig', [
            'controller_name' => 'SanteController',
        ]);
    }
    #[Route('/rendezvous/liste', name: 'rendezVousListe')]
    public function rendezVousListe(RendezVousRepository $repo, NotificationRepository $notificationRepository): Response
    {
        $usercurrent = $this->getUser();
        $rendezvous = $repo->findAll();
        $notifications = $notificationRepository->findBy(array('toUser' => $usercurrent));
        return $this->render('user/client/listerendezvousclient.html.twig', [
            'controller_name' => 'UserController',
            'user' => $usercurrent,
            'rendezvous' => $rendezvous,
            'notifications' => $notifications
        ]);
    }

    // HNEEE YE AMIRA AHAYAY
    #[Route('/sante/rendezvous/add/{id}', name: 'addRendezVous')]

    public function addRendezVous(
        Request $request,
        $id,
        ManagerRegistry $em,
        RealTimeManager $realTimeManager,
        NotificationRepository $notificationRepository,
        NormalizerInterface $normalizer,
        HubInterface $hub
    ): Response {
        $DoctorByID = $em->getRepository(User::class)->find($id);
        if ($DoctorByID->getRoles()[0] == 'ROLE_MEDCIN') {
            $rendezvous = new RendezVous();
            $user = $this->getUser();
            $notifications = $notificationRepository->findBy(array('toUser' => $user), array('dateNotification' => 'DESC'));
            $data = null;
            $form = $this->createForm(RendezVousType::class, $rendezvous);
            $form->handleRequest($request);
            $rendezvous->setFromuser($user);
            if ($form->isSubmitted()) {
                if ($request->request->get('radio')) {
                    $rendezvous->setDateRV(new \DateTime($request->request->get('radio')));
                    $rendezvous->setState('inconfirmed');
                    $rendezvous->setDatePassageRV(new \DateTime());
                    $rendezvous->setHourRV(new \DateTime());
                    $rendezvous->setHourPassageRV(new \DateTime());
                    $rendezvous->setFromuser($user);
                    $rendezvous->setTodoctor($DoctorByID);
                    $em = $em->getManager();
                    $em->persist($rendezvous);
                    $em->flush();
                    $notification = new Notification();
                    $notification->setDateNotification(new \DateTime());
                    $notification->setMessage('your rendez vous with' . $DoctorByID->getName());
                    $notification->setToUser($user);
                    $notification->setPath("rdv");
                    $notification->setSeen(false);
                    $notificationRepository->save($notification);
                    $notificationJSON = $normalizer->normalize($notification, 'json', ['groups' => "notification"]);
                    $json = json_encode($notificationJSON);
                    $realTimeManager->Walker($json, $hub);
                    return $this->redirectToRoute('rendezVousListe');
                } else {
                    $data = "please pick a date with doctor";
                    return $this->render('sante/addrendezvous.html.twig', [
                        'form' => $form->createView(),
                        'data' => $data,
                        'user' => $user,
                        'notifications' => $notifications,
                        'doctor' => $DoctorByID,
                    ]);
                }
            }
            return $this->render('sante/addrendezvous.html.twig', [
                'form' => $form->createView(),
                'data' => $data,
                'user' => $user,
                'notifications' => $notifications,
                'doctor' => $DoctorByID,
            ]);
        } else {
            return $this->redirectToRoute('app_error');
        }
    }


    #[Route('/dashboard/doctor/rendez-vous', name: 'listeRendezVousForDoctor')]
    public function ListeRendezVous(RendezVousRepository $repo , NotificationRepository $notificationRepository): Response
    {

        $usercurrent = $this->getUser();
        $rendezvous = $repo->findAll();

        $notifications = $notificationRepository->findBy(array(), array('dateNotification' => 'DESC'));
        if ($usercurrent->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->render('sante/rendezvous/listerendezvous.html.twig', [
                'controller_name' => 'SanteController',
                'user' => $usercurrent,
                'notifications' => $notifications , 
                'rendezvous' => $rendezvous
            ]);
        } else {
            return $this->render('user/doctor/Dashboarddoctor.html.twig', [
                'controller_name' => 'SanteController',
                'user' => $usercurrent,
                'rendezvous' => $rendezvous,
                'notifications' => $notifications 
            ]);
        }
    }


    #[Route('/dashboard/doctor/clientliste', name: 'ListeClientByDoctor')]
    public function ListeClientByDoctor(UserRepository $userRepository): Response
    {
        $User_client = $userRepository->findByRole('["ROLE_CLIENT"]');
        $user = $this->getUser();
        return $this->render('user/doctor/ListeClientDoctor.html.twig', [
            'controller_name' => 'SanteController',
            'User_client' => $User_client,
            'user' => $user
        ]);
    }
    #[Route('/dashboard/doctor/disponibilityliste', name: 'ListeDisponibility')]
    public function ListeDisponibility(Request $req, ManagerRegistry $em): Response
    {
        $dispo = new Disponibility();
        $dispo->setDateDispo(new \DateTime());
        $form = $this->createForm(DisponibilityType::class, $dispo);
        $form->handleRequest($req);

        $user = $this->getUser();
        $data = null;
        if ($form->isSubmitted()) {
            $dataF = $form->getData();

            if ($form->isValid()) {

                $dispo->setHeureStart(new \DateTime($req->request->get('HeureStart')));

                $dispo->setHeureEnd(new \DateTime($req->request->get('HeureEnd')));
                $dispo->setDoctor($user);

                $em = $em->getManager();
                $em->persist($dispo);
                $em->flush();
            } else {
                $data = "Failed to add new Disponibility Slote  please click in ";
                // $response = new JsonResponse($data);
                return $this->render('user/doctor/DisponibilityListe.html.twig', [
                    'controller_name' => 'SanteController',
                    'user' => $user,
                    'data' =>  $data,
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/doctor/DisponibilityListe.html.twig', [
            'controller_name' => 'SanteController',
            'user' => $user,
            'data' =>  $data,
            'form' => $form->createView()
        ]);
    }


    #[Route('/dashboard/doctor/disponibility/update/{id}', name: 'UpdateDisponibility')]
    public function UpdateDisponibility($id, Request $req, ManagerRegistry $em, DisponibilityRepository $repo): Response
    {
        $dispo = $repo->find($id);
        $form = $this->createForm(DisponibilityType::class, $dispo);
        $form->handleRequest($req);

        $user = $this->getUser();
        $data = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $dispo->setHeureStart(new \DateTime($req->request->get('HeureStart')));

                $dispo->setHeureEnd(new \DateTime($req->request->get('HeureEnd')));
                $dispo->setDoctor($user);
                $em = $em->getManager();
                $em->persist($dispo);
                $em->flush();
                return $this->redirectToRoute('ListeDisponibility');
            } else {
                $data = "Failed to add new Disponibility Slote to this Doctor please click in ";
                // $response = new JsonResponse($data);
                return $this->render('user/doctor/updateDisponibility.html.twig', [
                    'controller_name' => 'SanteController',
                    'user' => $user,
                    'data' =>  $data,
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('user/doctor/updateDisponibility.html.twig', [
            'controller_name' => 'SanteController',
            'user' => $user,
            'data' =>  $data,
            'dispo' => $dispo,
            'form' => $form->createView()
        ]);
    }


    #[Route('/dashboard/doctor/disponibility/delete/{id}', name: 'DeleteDisponibility')]
    public function DeleteDisponibility($id, ManagerRegistry $em, DisponibilityRepository $repo): Response
    {
        $dispo = $repo->find($id);
        $user = $dispo->getDoctor();
        $em = $em->getManager();
        $em->remove($dispo);
        $em->flush();

        $userConnected = $this->getUser();
        if ($userConnected->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('DoctorDetails', array('id' => $user->getId()));
        }
        return $this->redirectToRoute('ListeDisponibility');
    }



    #[Route('/dashboard/pharmacien/ordenance', name: 'ListeOrdenance')]
    public function ListeOrdenance(): Response
    {
        $userConnected = $this->getUser();
        return $this->render('user/pharmacien/pharmacienDash.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $userConnected,
        ]);
    }

    #[Route('/dashboard/pharmacien/medicament', name: 'ListeMedicament')]
    public function ListeMedicament(MedicamentRepository $repo, Request $req): Response
    {
        $Medicament = $repo->findAll();
        $userConnected = $this->getUser();

        $MedicamentToadd = new Medicament();
        $form = $this->createForm(AddMedicamentType::class, $MedicamentToadd);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $repo->save($MedicamentToadd, true);
                return $this->redirectToRoute('ListeMedicament');
            } else {
                $data = "Failed to add new Medicament  please click in ";
                $response = new JsonResponse($data);
                return $this->render('user/pharmacien/listemedicament.html.twig', [
                    'controller_name' => 'HomeController',
                    'user' => $userConnected,
                    'Medicaments' => $Medicament,
                    'data' => $response->getContent(),
                    'form' => $form->createView()

                ]);
            }
        }
        return $this->render('user/pharmacien/listemedicament.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $userConnected,
            'Medicaments' => $Medicament,
            'data' => "",
            'form' => $form->createView()

        ]);
    }
}
