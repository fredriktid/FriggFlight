<?php

namespace Frigg\FlightBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\Airline;
//use Frigg\FlightBundle\Form\FlightType;

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
            $airlineService->setEntityById($airlineId);
            return array(
                'success' => true,
                'data' => $airlineService->getData()
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
                'data' => $airlineService->getEntity()
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
        /*$airline = $this->getAirline($airlineId);
        $entity = new Flight();
        $entity->setAirline($airline);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airline_flight',
                    array(
                        'airlineId' => $entity->getAirline()->getId(),
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
     * @var integer $airlineId Id of the airline entity
     * @var integer $flightId Id of the flight entity
     * @return View|array
     */
    public function putAction(Request $request, $airlineId, $flightId)
    {
        /*$entity = $this->getEntity($airlineId, $flightId);
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
     * @var integer $airlineId Id of the airline entity
     * @var integer $flightId Id of the flight entity
     * @return View
     */
    public function deleteAction($airlineId, $flightId)
    {
        /*$entity = $this->getEntity($airlineId, $flightId);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        */
        return $this->view(null, Codes::HTTP_NO_CONTENT);

    }
}
