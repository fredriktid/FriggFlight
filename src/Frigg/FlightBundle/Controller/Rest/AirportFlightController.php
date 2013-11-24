<?php

namespace Frigg\FlightBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\Airport;
//use Frigg\FlightBundle\Form\FlightType;

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
            $service = $this->container->get('frigg_flight.airport_service');
            $service->setEntity($this->getAirport($airportId));
            return array(
                'success' => true,
                'data' => $service->getData()
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }
    }

    /**
     * Get action
     * @var integer $airportId Id of the airport entity
     * @var integer $flightId Id of the flight entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airportId, $flightId)
    {
        $entity = $this->getEntity($airportId, $flightId);

        return array(
            'data' => $entity,
        );
    }

    /**
     * Collection post action
     * @var Request $request
     * @var integer $airportId Id of the airport entity
     * @return View|array
     */
    public function cpostAction(Request $request, $airportId)
    {
        /*$airport = $this->getAirport($airportId);
        $entity = new Flight();
        $entity->setAirport($airport);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airport_flight',
                    array(
                        'airportId' => $entity->getAirport()->getId(),
                        'id' => $entity->getId()
                    )
                ),
                Codes::HTTP_CREATED
            );
        }

        return array(
            'form' => $form,
        );*/
        return array(
            'form' => false
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
        /*$entity = $this->getEntity($airportId, $flightId);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        return array(
            'form' => $form,
        );*/
        return array(
            'form' => false
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
        /*$entity = $this->getEntity($airportId, $flightId);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();*/

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Get flight instance
     * @var integer $airportId Id of the airport entity
     * @var integer $flightId Id of the flight entity
     * @return Flight
     */
    protected function getEntity($airportId, $flightId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airport' => $airportId,
            )
        );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find flight entity');
        }

        return $entity;
    }

    /**
     * Get airport instance
     * @var integer $airportId Id of the airport entity
     * @return Airport
     */
    protected function getAirport($airportId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FriggFlightBundle:Airport')->find($airportId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find airport entity');
        }

        return $entity;
    }

}
