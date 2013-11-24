<?php

namespace Frigg\FlightBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        $airlineService = $this->container->get('frigg_flight.airline_service');

        $airportService->setSession($request->query->get('airportId'), true);
        $airlineService->setSession($request->query->get('airlineId'), true);

        return $this->render('FriggFlightBundle:Dashboard:index.html.twig', array(
            'airportId' => $airportService->getSession(),
            'airlineId' => $airlineService->getSession()
        ));
    }
}
