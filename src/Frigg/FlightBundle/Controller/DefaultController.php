<?php

namespace Frigg\FlightBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="frigg_flight_homepage")
     */
    public function indexAction(Request $request)
    {
        $airportCode = $request->query->get('airport');

        return $this->render('FriggFlightBundle:Default:index.html.twig', array(
            'airport_code' => $airportCode,
            'airport' => false
        ));
    }
}
