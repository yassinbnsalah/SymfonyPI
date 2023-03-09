<?php

namespace App\Controller;

use App\Entity\Disponibility;
use App\Repository\DisponibilityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DisponibilityServiceController extends AbstractController
{
    #[Route('/disponibility/service', name: 'app_disponibility_service')]
    public function index(): Response
    {
        return $this->render('disponibility_service/index.html.twig', [
            'controller_name' => 'DisponibilityServiceController',
        ]);
    }


    #[Route('/displayDisponibility', name: 'displayDisponibility')]
    public function displayDisponibility(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        DisponibilityRepository $disponibilityRepository
    ): Response {
        $id = $request->query->get("id");
        $user = $userRepository->find($id);
        $disponibilites = $disponibilityRepository->findBy(array("doctor" => $user));

        $disponibilitesNormilizer = $normalizer->normalize($disponibilites, 'json', ['groups' => "disponibilites"]);
        return new JsonResponse($disponibilitesNormilizer);
    }


    #[Route('/detailDisponibility', name: 'detailDisponibility')]
    public function detailDisponibility(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        DisponibilityRepository $disponibilityRepository
    ): Response {
        $id = $request->query->get("id");
       
        $disponibilites = $disponibilityRepository->find($id);

        $disponibilitesNormilizer = $normalizer->normalize($disponibilites, 'json', ['groups' => "disponibilites"]);
        return new JsonResponse($disponibilitesNormilizer);
    }

    #[Route('/AddDispo', name: 'AddDispo')]
    public function AddDispo(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        DisponibilityRepository $disponibilityRepository
    ): Response {
        $id = $request->query->get("id");
        $heureStart = new \DateTime($request->query->get('HeureStart'));
        $heureEnd = new \DateTime($request->query->get('HeureEnd'));
        $dateDispo = new \DateTime($request->query->get('Datedispo'));
        $user = $userRepository->find($id); 
        $dispo = new Disponibility();
        $dispo->setDateDispo($dateDispo);
        $dispo->setHeureStart($heureStart);
        $dispo->setHeureEnd($heureEnd);
        $dispo->setNote("From Mobile");
        $dispo->setState("Available");
        $dispo->setDoctor($user);
        $disponibilityRepository->save($dispo);
      

        return new JsonResponse("Disponnibility created successfully");
    }


    #[Route('/updateStateDispo', name: 'updateState')]
    public function updateState(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        DisponibilityRepository $disponibilityRepository
    ): Response {
        $id = $request->query->get("id");
        $disponibilite = $disponibilityRepository->find($id);

        $state = $disponibilite->getState();
        if($state == "full"){
            $disponibilite->setState("available");
        }else{
            $disponibilite->setState("full");
        }
        $disponibilityRepository->save($disponibilite);
      

        return new JsonResponse("Disponnibility updated successfully");
    }


    
    #[Route('/deleteDispo', name: 'deleteDispo')]
    public function deleteDispo(
        Request $request,
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        DisponibilityRepository $disponibilityRepository
    ): Response {
        $id = $request->query->get("id");
        $disponibilite = $disponibilityRepository->find($id);

        $disponibilityRepository->remove($disponibilite);
      

        return new JsonResponse("Disponnibility removed successfully");
    }
}
