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
     *  section="Airports",
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
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('FriggFlightBundle:Airport')->findAll();
    }

    /**
     * @ApiDoc(
     *  section="Airports",
     *  resource=true,
     *  description="Returns a single airport object",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when airport does not exist"
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
        $airportService = $this->container->get('frigg.airport.flight');
        $airportService->setParentById($airportId);
        return $airportService->getParent();
    }

    /**
     * @ApiDoc(
     *  section="Airports",
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
        $airportService = $this->container->get('frigg.airport.flight');
        return $airportService->getAvinorAirports();
    }

    /**
     * @ApiDoc(
     *  section="Airports",
     *  tags="TO-DO",
     *  description="Generates and returns graph data"
     * )
     *
     * @Rest\View()
     */
    public function cgetGraphAction(Request $request, $airportId)
    {
        return null;
    }

    /**
     * @ApiDoc(
     *  section="Airports",
     *  resource=true,
     *  description="Creates an airport and returns the view",
     *  statusCodes={
     *      201="Returned when airport was successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      403="Returned when the request was not authorized"
     *  }
     * )
     *
     * @return View|array
     */
    public function cpostAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied. Only administrators may add data to the API.');
        }

        // Create form type from entity
        $airportEntity = new Airport();
        $form = $this->createForm(new AirportType(), $airportEntity);

        // Validate request data on form
        $form->bind($request);
        if ($form->isValid()) {

            // Save new airport entity
            $em = $this->getDoctrine()->getManager();
            $em->persist($airportEntity);
            $em->flush();

            // Redirect to entities new view (HTTP 201)
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

        // Else return the form to client
        return array(
            'form' => $form,
        );
    }

    /**
     * @ApiDoc(
     *  section="Airports",
     *  resource=true,
     *  description="Quickcreates an airport",
     *  statusCodes={
     *      204="Returned when the flight has been successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      404={
     *          "Returned when airport does not exist",
     *          "Or when flight does not exist"
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied. Only administrators may add data to the API.');
        }

        // Load the service
        $airportService = $this->container->get('frigg.airport.flight');

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
     *  section="Airports",
     *  resource=true,
     *  description="Delete an airport",
     *  statusCodes={
     *      204="Returned when airport was successfully deleted",
     *      403="Returned when the request was not authorized",
     *      404="Returned when airport does not exist"
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied. Only administrators may delete data from the API');
        }

        // Load service
        $airportService = $this->container->get('frigg.airport.flight');

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
