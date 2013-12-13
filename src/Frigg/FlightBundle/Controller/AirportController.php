<?php

namespace Frigg\FlightBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AirportController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Route("/airports", name="airports_home")
     */
    public function indexAction(Request $request)
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        $airportService->setSession($request->query->get('airportId'), true);

        try {
            $currentAirport = $airportService->getSessionEntity();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->render('FriggFlightBundle:Airport:index.html.twig', array(
            'current_airport' => $currentAirport,
            'avinor_airports' => $airportService->getAvinorAirports()
        ));
    }
}
