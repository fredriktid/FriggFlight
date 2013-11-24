<?php

namespace Frigg\FlightBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Form\FlightType;

class AirportFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Collection get action
     * @var Request $request
     * @var integer $airportId Id of the airport entity
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request, $airportId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setParentById($airportId);
            return array(
                'success' => true,
                'data' => $airportService->getFlights()
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
     * @var integer $airportId Id of the airport entity
     * @var integer $flightId Id of the flight entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airportId, $flightId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setFlightById($airportId, $flightId);
            return array(
                'success' => true,
                'data' => $airportService->getFlight()
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
     * @var integer $airportId Id of the airport entity
     * @return View|array
     */
    public function cpostAction(Request $request, $airportId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setParentById($airportId);
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $airportEntity = $airportService->getParent();

        $flightEntity = new Flight();
        $flightEntity->setAirport($airportEntity);
        $form = $this->createForm(new FlightType(), $flightEntity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($flightEntity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airport_flight',
                    array(
                        'airportId' => $flightEntity->getAirport()->getId(),
                        'flightId' => $flightEntity->getId()
                    )
                ),
                Codes::HTTP_CREATED
            );
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Put action
     * @var Request $request
     * @var integer $airportId Id of the airport entity
     * @var integer $flightId Id of the flight entity
     * @return View|array
     */
    public function putAction(Request $request, $airportId, $flightId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setFlightById($airportId, $flightId);
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $flightEntity = $airportService->getFlight();
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
     * @var integer $airportId Id of the airport entity
     * @var integer $flightId Id of the flight entity
     * @return View
     */
    public function deleteAction($airportId, $flightId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setFlightById($airportId, $flightId);
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $flightEntity = $airportService->getFlight();
        $em = $this->getDoctrine()->getManager();
        $em->remove($flightEntity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
