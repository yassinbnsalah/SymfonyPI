<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\AddMedicamentType;
use App\Repository\MedicamentRepository;
use App\Repository\NotificationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MedicammentController extends AbstractController
{
    #[Route('/medicamment', name: 'app_medicamment')]
    public function index(): Response
    {

        return $this->render('medicamment/index.html.twig', [
            'controller_name' => 'MedicammentController',
        ]);
    }
    #[Route('/medicament/showMedicament', name: 'showMedicament')]
    public function showMedicamment(MedicamentRepository $repo)
    {
        $Medicament = $repo->findAll();
        return $this->render('medicamment/showMedicamment.html.twig', [
            'controller_name' => 'MedicammentController',
            'Medicamment' => $Medicament,

        ]);
    }

    #[Route('/medicament/add', name: 'AddMedicament')]
    public function AddPatient(MedicamentRepository $repo, Request $req, NotificationRepository $notificationRepository): Response
    {
        $Medicament = new Medicament();
        $form = $this->createForm(AddMedicamentType::class, $Medicament);
        $userConnected = $this->getUser();

        $notifications = $notificationRepository->findBy(array('toUser' => $userConnected), array('dateNotification' => 'DESC'));

        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $repo->save($Medicament, true);
        }
        return $this->render('medicament/addMedicamment.html.twig', [
            'f' => $form->createView(),
            'user' =>  $userConnected,
            'notifications' => $notifications
        ]);
    }
    #[Route('/doctor/medicament/{id}', name: 'UpdateMedicament')]
    public function UpdateMedicament(MedicamentRepository $repo, $id, ManagerRegistry $doctrine, Request  $request, NotificationRepository $notificationRepository): Response

    {
        $userConnected = $this->getUser();
        $Medicament = $doctrine->getRepository(Medicament::class)->find($id);
        $form = $this->createForm(AddMedicamentType::class, $Medicament);
        $form->handleRequest($request);


        $notifications = $notificationRepository->findBy(array('toUser' => $userConnected), array('dateNotification' => 'DESC'));
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $repo->save($Medicament, true);
                return $this->redirectToRoute('ListeMedicament');
            } else {
                $data = "Failed to Update Medicament  please try again";
                $response = new JsonResponse($data);
                return $this->render('user/pharmacien/updatemedicament.html.twig', [
                    'controller_name' => 'MedicammentController',
                    'Medicament' => $Medicament,
                    'user' => $userConnected,
                    'notifications' => $notifications,
                    'data' => $response->getContent(),
                    'form' => $form->createView()

                ]);
            }
        }
        return $this->render('user/pharmacien/updatemedicament.html.twig', [
            'controller_name' => 'MedicammentController',
            'Medicament' => $Medicament,
            'user' => $userConnected,
            'data' => "",
            'notifications' => $notifications,
            'form' => $form->createView()

        ]);
    }


    #[Route("medicament/delete/{id}", name: 'deleteMedicament')]
    public function deleteMedicament($id, ManagerRegistry $doctrine)
    {
        $c = $doctrine
            ->getRepository(Medicament::class)
            ->find($id);
        $em = $doctrine->getManager();
        $em->remove($c);
        $em->flush();

        return $this->redirectToRoute('ListeMedicament');
    }
}
