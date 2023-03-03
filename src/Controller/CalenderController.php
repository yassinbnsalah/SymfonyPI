<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use CalendarBundle\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
class CalenderController extends AbstractController
{
    #[Route('/calender', name: 'app_calender')]
    public function index(): Response
    {
        return $this->render('calender/index.html.twig', [
            'controller_name' => 'CalenderController',
        ]);
    }
    public function loadAction(Request $request): Response
    {
        $start = new \DateTime($request->get('start'));
        $end = new \DateTime($request->get('end'));
        $filters = $request->get('filters', '{}');
        $filters = \is_array($filters) ? $filters : json_decode($filters, true);

        $event = $this->dispatchWithBC(
            new CalendarEvent($start, $end, $filters),
            CalendarEvents::SET_DATA
        );
        $content = $this->serializer->serialize($event->getEvents());

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($content);
        $response->setStatusCode(empty($content) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK);

        return $response;
    }
    public function dispatchWithBC($event, ?string $eventName = null)
    {
        if ($this->eventDispatcher instanceof ContractsEventDispatcherInterface) {
            return $this->eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
