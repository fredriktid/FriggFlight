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
        $airportCode = $request->query->get('airport');
        $airlineCode = $request->query->get('airline');

        return $this->render('FriggFlightBundle:Dashboard:index.html.twig', array(
            'airport_code' => $airportCode,
            'airline_code' => $airlineCode
        ));
    }
}
