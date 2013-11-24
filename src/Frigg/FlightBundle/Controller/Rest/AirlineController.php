<?php

namespace Frigg\FlightBundle\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Form\AirlineType;

class AirlineController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Collection get action
     * @var Request $request
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request)
    {
        $airlineService = $this->container->get('frigg_flight.airline_service');
        return array(
            'success' => true,
            'data' => $airlineService->getAll()
        );
    }

    /**
     * Get airline instance
     * @var integer $airlineId Id of the entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airlineId)
    {
        try {
            $airlineService = $this->container->get('frigg_flight.airline_service');
            $airlineService->setParentById($airlineId);
            return array(
                'success' => true,
                'data' => $airlineService->getParent(),
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }
    }

    /**
     * Collection of delayed airline flights
     * @var integer $airlineId Id of the entity's airline
     * @return array
     *
     * @Rest\View()
     */
    public function cgetDelayedAction($airlineId)
    {
        return array();
    }


    /**
     * Collection post action
     * @var Request $request
     * @return View|array
     */
    public function cpostAction(Request $request)
    {
        $airlineEntity = new Airline();
        $form = $this->createForm(new AirlineType(), $airlineEntity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($airlineEntity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airline',
                    array(
                        'airlineId' => $airlineEntity->getId()
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
     * @var integer $airlineId Id of the entity
     * @return View|array
     */
    public function putAction(Request $request, $airlineId)
    {
        $airlineService = $this->container->get('frigg_flight.airline_service');

        try {
            $airlineService->setParentById($airlineId);
            $airlineEntity = $airlineService->getParent();
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $form = $this->createForm(new AirlineType(), $airlineEntity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($airlineEntity);
            $em->flush();

            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Delete action
     * @var integer $airlineId Id of the entity
     * @return View
     */
    public function deleteAction($airlineId)
    {
        $airlineService = $this->container->get('frigg_flight.airline_service');

        try {
            $airlineService->setParentById($airlineId);
            $airlineEntity = $airlineService->getParent();
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($airlineEntity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
