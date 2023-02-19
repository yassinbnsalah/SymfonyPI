<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\AddMedicamentType;
use App\Repository\MedicamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

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
    public function AddPatient(MedicamentRepository $repo, Request $req): Response
    {
        $Medicament = new Medicament();
        $form = $this->createForm(AddMedicamentType::class, $Medicament);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $repo->save($Medicament, true);
        }
        return $this->render('medicament/addMedicamment.html.twig', [
            'f' => $form->createView()
        ]);
    }
    #[Route('/doctor/medicament/{id}', name: 'UpdateMedicament')]
    public function UpdateMedicament(MedicamentRepository $repo, $id, ManagerRegistry $doctrine, Request  $request): Response

    {
        $userConnected = $this->getUser(); 
        $Medicament = $doctrine->getRepository(Medicament::class)->find($id);
        $form = $this->createForm(AddMedicamentType::class, $Medicament);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $repo->save($Medicament, true);
            return $this->redirectToRoute('ListeMedicament'); 
        }
        return $this->render('user/pharmacien/updatemedicament.html.twig', [
            'controller_name' => 'MedicammentController',
            'Medicament' => $Medicament,
            'user' => $userConnected,
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