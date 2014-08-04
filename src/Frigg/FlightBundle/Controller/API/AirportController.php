<?php

namespace Frigg\FlightBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Frigg\FlightBundle\Entity\Airport;
use Frigg\FlightBundle\Form\AirportType;

class AirportController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @ApiDoc(
     *  section="Airport",
     *  resource=true,
     *  description="Returns a collection of all airports",
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request)
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        return $airportService->getAll();
    }

    /**
     * @ApiDoc(
     *  section="Airport",
     *  resource=true,
     *  description="Returns a collection of Avinor airports",
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     *
     * @Rest\View()
     */
    public function cgetAvinorAction()
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        return $airportService->getAvinorAirports();
    }

    /**
     * @ApiDoc(
     *  section="Airport",
     *  tags="TO-DO",
     *  description="Generate data to use in graphs"
     * )
     *
     * @Rest\View()
     */
    public function cgetGraphAction(Request $request, $airportId)
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        $airportService->setParentById($airportId);
        return null;
    }

    /**
     * @ApiDoc(
     *  section="Airport",
     *  resource=true,
     *  description="Returns a single airport object",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when airport was not found"
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"},
     *  }
     * )
     *
     * @Rest\View()
     */
    public function getAction($airportId)
    {
        $airportService = $this->container->get('frigg_flight.airport_service');
        $airportService->setParentById($airportId);
        return $airportService->getParent();
    }

    /**
     * @ApiDoc(
     *  section="Airport",
     *  resource=true,
     *  description="Creates an airport and returns the view",
     *  statusCodes={
     *      201="Returned when airport was successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      403="Returned when the request was not authorized to create an airport"
     *  }
     * )
     *
     * @return View|array
     */
    public function cpostAction(Request $request)
    {
        $airportEntity = new Airport();
        $form = $this->createForm(new AirportType(), $airportEntity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($airportEntity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airport',
                    array(
                        'airportId' => $airportEntity->getId()
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
     * @ApiDoc(
     *  section="Airport",
     *  resource=true,
     *  description="Quickcreate an airport",
     *  statusCodes={
     *      204="Returned when the flight has been successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      404={
     *          "Returned when airport was not found",
     *          "Or when flight was not found"
     *      }
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"},
     *  }
     * )
     *
     * @return View|array
     */
    public function putAction(Request $request, $airportId)
    {
        // Load the service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set given airport in service
        $airportService->setParentById($airportId);

        // Get airport entity and create a form instance
        $airportEntity = $airportService->getParent();
        $form = $this->createForm(new AirportType(), $airportEntity);

        // Validate form
        $form->bind($request);
        if ($form->isValid()) {
            // Save new airport entity
            $em = $this->getDoctrine()->getManager();
            $em->persist($airportEntity);
            $em->flush();

            // Return empty view (HTTP 204)
            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        // Else return the form again
        return array(
            'form' => $form,
        );
    }

    /**
     * @ApiDoc(
     *  section="Airport",
     *  resource=true,
     *  description="Delete an airport",
     *  statusCodes={
     *      204="Returned when airport was successfully deleted",
     *      403="Returned when the request was not authorized",
     *      404="Returned when airport was not found"
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function deleteAction($airportId)
    {
        // Load service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set airport in service
        $airportService->setParentById($airportId);

        // Delete current airport
        $airportEntity = $airportService->getParent();
        $em = $this->getDoctrine()->getManager();
        $em->remove($airportEntity);
        $em->flush();

        // Return empty view (HTTP 204)
        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
