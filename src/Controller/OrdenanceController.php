<?php

namespace App\Controller;

use App\Entity\Ordennance;
use App\Entity\OrdennanceLigne;
use App\Form\OrdennanceType;
use App\Repository\MedicamentRepository;
use App\Repository\OrdennanceLigneRepository;
use App\Repository\OrdennanceRepository;
use App\Repository\RendezVousRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdenanceController extends AbstractController
{
    #[Route('/ordenance', name: 'app_ordenance')]
    public function index(): Response
    {
        return $this->render('ordenance/index.html.twig', [
            'controller_name' => 'OrdenanceController',
        ]);
    }

    #[Route('/rendezvous/generate/ordenance/{id}', name: 'GenerateOrdenance')]
    public function GenerateOrdenance(Request $request , $id, OrdennanceLigneRepository $ordlig , RendezVousRepository $repo,MedicamentRepository $medicamentRepo ,ManagerRegistry $em): Response
    {
        $user = $this->getUser(); 
        $rendezvous = $repo->find($id) ; 
        $medicaments = $medicamentRepo->findAll(); 
        $ordennance = new Ordennance() ; 
        $form = $this->createForm(OrdennanceType::class, $ordennance);
        $form->handleRequest($request);
      //  dd($medicaments);
      if($form->isSubmitted()){
        $rendezvous->setOrdennance($ordennance); 
        $ordennance->setDateordenance(new \DateTime() ) ; 
        $ordennance->setAmount(0) ; 
        $ordennance->setRendezVous($rendezvous); 
        $em1=$em->getManager();
        $em1->persist($ordennance);
        $em1->flush() ; 
        $rendezvous->setOrdennance($ordennance); 
        $repo->save($rendezvous);
        foreach ($medicaments as $medi){
       
             if($request->request->get('ch'.(string)$medi->getId()) == true ) {
                $ide = "qte" . (string)$medi->getId() ; 
                // dd($request->request->get($ide));
                $ordLigne = new OrdennanceLigne(); 
                $ordLigne->setQunatite($request->request->get($ide));
                $ordLigne->setMedicament($medi) ; 
                $ordLigne->setOrdennance($ordennance) ; 
                $ordlig->save($ordLigne) ; 
               
                
             }
           
            
         }
         return $this->redirectToRoute('doctorrendezdetails', array('id' => $id)); 
      }
     
        return $this->render('user/doctor/DoctorGenerateOrdenance.html.twig', [
            'controller_name' => 'OrdenanceController',
            'user' => $user,
            'form' => $form->createView(),
            'medicaments' => $medicaments,
            'rendezvous' => $rendezvous
        ]);
    }

    // #[Route('/rendezvous/update/ordenance/{id}', name: 'UpdateOrdenance')]
    // public function UpdateOrdenance(Request $request , $id, OrdennanceLigneRepository $ordlig
    //  , RendezVousRepository $repo,MedicamentRepository $medicamentRepo ,ManagerRegistry $em , OrdennanceRepository $ordRepo): Response
    // {
    //     $user = $this->getUser(); 
    //     $ordennance = $ordRepo->find($id) ; 
    //     $medicaments = $medicamentRepo->findAll(); 
    //     $ordennance = new Ordennance() ; 
    //     $form = $this->createForm(OrdennanceType::class, $ordennance);
    //     $form->handleRequest($request);
    //   //  dd($medicaments);
    //   if($form->isSubmitted()){
     
    //      return $this->redirectToRoute('doctorrendezdetails', array('id' => $id)); 
    //   }
     
    //     return $this->render('user/doctor/DoctorGenerateOrdenance.html.twig', [
    //         'controller_name' => 'OrdenanceController',
    //         'user' => $user,
    //         'form' => $form->createView(),
    //         'medicaments' => $medicaments,
    //         'rendezvous' => $rendezvous
    //     ]);
    // }
}
