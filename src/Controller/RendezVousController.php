<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\RendezVousType;
use App\Form\UpdateRendezVousType;
use App\Manager\RealTimeManager;
use App\Repository\NotificationRepository;
use App\Repository\OrdennanceRepository;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous', name: 'app_rendez_vous')]
    public function index(): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'controller_name' => 'RendezVousController',
        ]);
    }
    #[Route('/rendez-vous/add/{id}', name: 'addRendezVous1')]
    public function addRendezVous(
        Request $request,
        $id,
        ManagerRegistry $em,
        HubInterface $hub,
        RealTimeManager $realTimeManager,
        NotificationRepository $notificationRepository,
        NormalizerInterface $normalizer
    ): Response {
        $DoctorByID = $em->getRepository(User::class)->find($id);
        $rendezvous = new RendezVous();
        $user = $this->getUser();
        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);
        $rendezvous->setFromuser($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $rendezvous->setState('inconfirmed');
            $rendezvous->setDatePassageRV(new \DateTime());

            $rendezvous->setFromuser($user);
            $rendezvous->setTodoctor($DoctorByID);
            $em = $em->getManager();
            $em->persist($rendezvous);
            $em->flush();
            $notification = new Notification() ; 
            $notification->setDateNotification(new \DateTime()); 
            $notification->setMessage('your rendez vous with'.$DoctorByID->getName()) ; 
            $notification->setToUser($user) ;
            $notification->setPath("rdv") ;
            $notification->setSeen(false);
            $notificationRepository->save($notification); 
            $notificationJSON = $normalizer->normalize($notification  , 'json', ['groups' => "notification"]);
            $json = json_encode($notificationJSON);
            $realTimeManager->Walker($json,$hub);

            return $this->redirectToRoute('listeRendezVous');
        }
        return $this->render('sante/addrendezvous.html.twig', [
            'form' => $form->createView(),
            'doctor' => $DoctorByID,
        ]);
    }

    #[Route('/rendez-vous/update/{id}', name: 'updateRendezVous')]
    public function updateRendezVous(Request $request, $id, ManagerRegistry $em): Response
    {
        $rendezvous = $em->getRepository(RendezVous::class)->find($id);

        $user = $this->getUser();
        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);
        $rendezvous->setFromuser($user);
        if ($form->isSubmitted()) {

            $em = $em->getManager();
            $em->persist($rendezvous);
            $em->flush();
            return $this->redirectToRoute('listeRendezVous');
        }
        return $this->render('sante/updateRendezVous.html.twig', [
            'form' => $form->createView(),
            'doctor' => $user,
        ]);
    }

    #[Route('/rendez-vous/confirme/{id}', name: 'confirmerendezvous')]
    public function confirmerendezvous($id, RendezVousRepository $repo, ManagerRegistry $em): Response
    {
        $user = $this->getUser();
        $rdvtoconfirm = $repo->find($id);
        $rdvtoconfirm->setState("confirm");
        $em = $em->getManager();
        $em->persist($rdvtoconfirm);
        $em->flush();
        return $this->redirectToRoute('listeRendezVousForDoctor');
    }
    #[Route('/rendez-vous/cancel/{id}', name: 'cancelrdv')]
    public function CancelRendezVous($id, RendezVousRepository $repo, ManagerRegistry $em): Response
    {
        $rdvtocancel = $repo->find($id);
        $rdvtocancel->setState("Cancel");
        $em = $em->getManager();
        $em->persist($rdvtocancel);
        $em->flush();
        $user = $this->getUser();
        // if($user->getRoles()[0] == 'ROLE_DOCTOR'){
        //     return $this->redirectToRoute('rendevousListes', array('id' => $rdvtocancel->getTodoctor()->getId())); 
        // }
        if ($user->getRoles()[0] == 'ROLE_MEDCIN') {
            return $this->redirectToRoute('listeRendezVousForDoctor');
        } else {
            return $this->redirectToRoute('rendezVousListe');
        }
    }

    #[Route('/client/rendez-vous/{id}', name: 'rendezdetails')]
    public function rendezdetails($id, RendezVousRepository $repo, ManagerRegistry $em, OrdennanceRepository $ordrepo): Response
    {
        $rdvdetails = $repo->find($id);

        $user = $this->getUser();
        $ordenances = $ordrepo->findAll();

        return $this->render('user/client/rendezvousdetails.html.twig', [
            'user' => $user,
            'rendezVous' => $rdvdetails,
            'ordenances' => $ordenances
        ]);
    }

    #[Route('/doctor/rendez-vous/{id}', name: 'doctorrendezdetails')]
    public function doctorrendezdetails(Request $request, $id, OrdennanceRepository $ordrepo, RendezVousRepository $repo, ManagerRegistry $em): Response

    {

        $rdvtoupdate = $repo->find($id);
        $data = null;
        $user = $this->getUser();

        $form = $this->createForm(UpdateRendezVousType::class, $rdvtoupdate);
        $form->handleRequest($request);
        $rdvdetails = $repo->find($id);
        $ordenances =  $ordrepo->findAll();
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $em->getManager();
                $em->persist($rdvtoupdate);
                $em->flush();
                return $this->render('user/doctor/rendezvousdetails.html.twig', [
                    'user' => $user,
                    'data' => $data,
                    'ordenances' => $ordenances,
                    'form' => $form->createView(),
                    'rendezVous' => $rdvdetails
                ]);
            } else {
                $data = "can not update rendez vous check";
                $rdvdetails1 = $repo->find($id);
                return $this->render('user/doctor/rendezvousdetails.html.twig', [
                    'user' => $user,
                    'data' => $data,
                    'ordenances' => $ordenances,
                    'form' => $form->createView(),
                    'rendezVous' => $rdvdetails1
                ]);
            }
        }
        return $this->render('user/doctor/rendezvousdetails.html.twig', [
            'user' => $user,
            'data' => $data,
            'ordenances' => $ordenances,
            'form' => $form->createView(),
            'rendezVous' => $rdvdetails
        ]);
    }
}
