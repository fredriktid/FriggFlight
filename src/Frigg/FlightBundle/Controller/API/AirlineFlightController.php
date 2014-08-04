<?php

namespace Frigg\FlightBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Form\FlightType;

class AirlineFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Collection of airline flights
     * @var Request $request
     * @var integer $airlineId Id of the airline entity
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request, $airlineId)
    {
        try {
            $airlineService = $this->container->get('frigg_flight.airline_service');
            $airlineService->setParentById($airlineId);
            return array(
                'success' => true,
                'data' => $airlineService->getFlightGroup()
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }
    }


    /**
     * Get flight instance
     * @var integer $airlineId Id of the airline entity
     * @var integer $flightId Id of the flight entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airlineId, $flightId)
    {
        try {
            $airlineService = $this->container->get('frigg_flight.airline_service');
            $airlineService->setFlightById($airlineId, $flightId);
            return array(
                'success' => true,
                'data' => $airlineService->getFlight()
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }
    }

    /**
     * Collection post action
     * @var Request $request
     * @var integer $airlineId Id of the airline entity
     * @return View|array
     */
    public function cpostAction(Request $request, $airlineId)
    {
        try {
            $airlineService = $this->container->get('frigg_flight.airline_service');
            $airlineService->setParentById($airlineId);
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $airlineEntity = $airlineService->getParent();

        $flightEntity = new Flight();
        $flightEntity->setAirline($airlineEntity);
        $form = $this->createForm(new FlightType(), $flightEntity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($flightEntity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airline_flight',
                    array(
                        'airlineId' => $flightEntity->getAirline()->getId(),
                        'flightId' => $flightEntity->getId()
                    )
                ),
                Codes::HTTP_CREATED
            );
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Put action
     * @var Request $request
     * @var integer $airlineId Id of the airline entity
     * @var integer $flightId Id of the flight entity
     * @return View|array
     */
    public function putAction(Request $request, $airlineId, $flightId)
    {
        try {
            $airlineService = $this->container->get('frigg_flight.airline_service');
            $airlineService->setFlightById($airlineId, $flightId);
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $flightEntity = $airlineService->getFlight();
        $form = $this->createForm(new FlightType(), $flightEntity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($flightEntity);
            $em->flush();

            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Delete action
     * @var integer $airlineId Id of the airline entity
     * @var integer $flightId Id of the flight entity
     * @return View
     */
    public function deleteAction($airlineId, $flightId)
    {
        try {
            $airlineService = $this->container->get('frigg_flight.airline_service');
            $airlineService->setFlightById($airportId, $flightId);
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $flightEntity = $airlineService->getFlight();

        $em = $this->getDoctrine()->getManager();
        $em->remove($flightEntity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);

    }
}
