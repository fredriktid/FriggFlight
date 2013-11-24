<?php

namespace Frigg\FlightBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Airport;
//use Frigg\FlightBundle\Form\AirportType;

class AirportController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Collection of all airports
     * @var Request $request
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request)
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        return array(
            'data' => $airportService->getAll()
        );
    }

    /**
     * Collection of avinor airports
     * @var Request $request
     * @return array
     *
     * @Rest\View()
     * @Get
     */
    public function avinorAction()
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        return array(
            'data' => $airportService->getAvinorAirports()
        );
    }

    /**
     * Graph data for given airpot
     * @var Request $request
     * @var integer $airportId Id of the entity
     * @return array
     *
     * @Rest\View()
     * @Get
     */
    public function graphAction(Request $request, $airportId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setEntityById($airportId);
            return array(
                'success' => false,
                'data' => array()
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
     * @var integer $airportId Id of the entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airportId)
    {
        try {
            $airportService = $this->container->get('frigg_flight.airport_service');
            $airportService->setEntityById($airportId);
            return array(
                'success' => true,
                'data' => $airportService->getEntity(),
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
     * @return View|array
     */
    public function cpostAction(Request $request)
    {
        /*$entity = new Airport();
        $form = $this->createForm(new AirportType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airport',
                    array('id' => $entity->getId())
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
     * @var integer $airportId Id of the entity
     * @return View|array
     */
    public function putAction(Request $request, $airportId)
    {
        /*$entity = $this->getEntity($airportId);
        $form = $this->createForm(new AirportType(), $entity);
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
     * @var integer $airportId Id of the entity
     * @return View
     */
    public function deleteAction($airportId)
    {
        /*$entity = $this->getEntity($airportId);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();*/

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
