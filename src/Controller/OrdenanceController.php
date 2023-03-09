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
    public function GenerateOrdenance(
        Request $request,
        $id,
        OrdennanceRepository $ordRepo , 
        OrdennanceLigneRepository $ordlig,
        RendezVousRepository $repo,
        MedicamentRepository $medicamentRepo,
        ManagerRegistry $em
    ): Response {
        $user = $this->getUser();
        $rendezvous = $repo->find($id);
        $medicaments = $medicamentRepo->findAll();
        $ordennance = new Ordennance();
        $form = $this->createForm(OrdennanceType::class, $ordennance);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $rendezvous->setOrdennance($ordennance);
            $ordennance->setDateordenance(new \DateTime());
            $ordennance->setAmount(0);
            $ordennance->setRendezVous($rendezvous);
            $em1 = $em->getManager();
            $em1->persist($ordennance);
            $em1->flush();
            $rendezvous->setOrdennance($ordennance);
            $repo->save($rendezvous);
            $amount = 0 ; 
            // here is the  change 
            foreach ($medicaments as $medi) {
                if ($request->request->get('ch' . (string)$medi->getId()) == true) {
                    $ide = "qte" . (string)$medi->getId();
                    $ordLigne = new OrdennanceLigne();
                    $ordLigne->setQunatite($request->request->get($ide));
                    $ordLigne->setMedicament($medi);
                    $ordLigne->setOrdennance($ordennance);
                    $ordlig->save($ordLigne);
                    $amount = $amount + $request->request->get($ide)*$medi->getPrix() ; 
                }
            }
            $ordennance->setAmount($amount);
            $ordRepo->save($ordennance) ; 
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

    #[Route('/rendezvous/update/ordenance/{id}', name: 'UpdateOrdenance')]
    public function UpdateOrdenance(
        Request $request,
        $id,
        OrdennanceLigneRepository $ordlig,
        RendezVousRepository $repo,
        MedicamentRepository $medicamentRepo,
    ): Response {
        $user = $this->getUser();
        $rendezvous = $repo->find($id);
        $medicaments = $medicamentRepo->findAll();
        $ordennance = $rendezvous->getOrdennance();
        $form = $this->createForm(OrdennanceType::class, $ordennance);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            foreach ($medicaments as $medi) {
                if ($request->request->get('ch' . (string)$medi->getId()) == true) {
                    $ide = "qte" . (string)$medi->getId();
                    $test = false;
                    foreach ($ordennance->getOrdennanceLignes() as $ordligne) {
                        if ($ordligne->getMedicament() == $medi) {
                            $ordligne->setQunatite($request->request->get($ide));
                            $ordlig->save($ordligne);
                            $test = true;
                        }
                    }
                    if ($test == false) {
                        $ordLigne = new OrdennanceLigne();
                        $ordLigne->setQunatite($request->request->get($ide));
                        $ordLigne->setMedicament($medi);
                        $ordLigne->setOrdennance($ordennance);
                        $ordlig->save($ordLigne);
                    }
                }
            }
            foreach ($ordennance->getOrdennanceLignes() as $OrdLigner) {
                if ($request->request->get('ch' . (string)$OrdLigner->getMedicament()->getId()) == false) {
                    $ordlig->remove($OrdLigner);
                }
            }
            return $this->redirectToRoute('doctorrendezdetails', array('id' => $id));
        }
        return $this->render('user/doctor/DoctorUpdateOrdenance.html.twig', [
            'controller_name' => 'OrdenanceController',
            'user' => $user,
            'ordenance' => $ordennance,
            'form' => $form->createView(),
            'medicaments' => $medicaments,
            'rendezvous' => $rendezvous
        ]);
    }

    #[Route('/ordenancePDF/{id}', name: 'ordenancePDF')]
    public function ordenancePDF($id, RendezVousRepository $OrdennanceRepo,OrdennanceLigneRepository $OrdennanceLigne ): Response
    { 
        $order = $OrdennanceRepo->find($id);
        $OrdennanceL = $OrdennanceLigne->findAll();
        return $this->render('/ordenance/ordenancePDF.html.twig', [
            'order' => $order,
            'OrdennanceL' => $OrdennanceL
        ]);
    }
}
