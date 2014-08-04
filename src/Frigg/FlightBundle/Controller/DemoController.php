<?php

namespace Frigg\FlightBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    /**
     * @Route("/demo", name="demo")
     */
    public function indexAction(Request $request)
    {
        // Load airport service as an example
        $airportService = $this->container->get('frigg.airport.flight');

        // Try to remember last airport
        $airportService->setSession($request->query->get('airportId'), true);

        // Return data to template
        return $this->render('FriggFlightBundle:Demo:index.html.twig', array(
            'current_airport' => $airportService->getSessionEntity(),
            'avinor_airports' => $airportService->getAvinorAirports()
        ));
    }
}
