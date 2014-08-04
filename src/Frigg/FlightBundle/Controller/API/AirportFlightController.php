<?php

namespace Frigg\FlightBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Form\FlightType;

class AirportFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Returns a group of flights from an airport that matches the filters",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the airport was not found"
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"}
     *  },
     *  filters={
     *      {"name"="direction", "dataType"="string", "description"="Switch between arriving and departing flights", "pattern"="A|D", "default"="D"},
     *      {"name"="from_time", "dataType"="integer", "description"="From a certain timestamp"},
     *      {"name"="to_time", "dataType"="integer", "description"="To another timestamp"},
     *      {"name"="is_delayed", "dataType"="string", "description"="Only include delayed flights", "pattern"="Y|N", "default"="N"}
     *  }
     * )
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request, $airportId)
    {
        // Load airport service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set optional filters in service
        $airportService
            ->setParam('direction', $request->query->get('direction'))
            ->setParam('from_time', $request->query->get('from_time'))
            ->setParam('to_time', $request->query->get('to_time'))
            ->setParam('is_delayed', $request->query->get('is_delayed'));

        // Associate with current airport
        $airportService->setParentById($airportId);

        // Fetch all flights matchin the filters
        $airportFlights = $airportService->getFlightGroup();

        // Return data
        return $airportFlights;
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Returns count of flights from an airport that matches the filters",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the airport was not found"
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"}
     *  },
     *  filters={
     *      {"name"="direction", "dataType"="string", "description"="Switch between arriving and departing flights", "pattern"="A|D", "default"="D"},
     *      {"name"="from_time", "dataType"="integer", "description"="From a certain timestamp"},
     *      {"name"="to_time", "dataType"="integer", "description"="To another timestamp"},
     *      {"name"="is_delayed", "dataType"="string", "description"="Only include delayed flights", "pattern"="Y|N", "default"="N"}
     *  }
     * )
     *
     * @Rest\View()
     */
    public function cgetCountAction(Request $request, $airportId)
    {
        // Load service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set filters in service
        $airportService
            ->setParam('direction', $request->query->get('direction'))
            ->setParam('from_time', $request->query->get('from_time'))
            ->setParam('to_time', $request->query->get('to_time'))
            ->setParam('is_delayed', $request->query->get('is_delayed'));

        // Associate with current airport
        $airportService->setParentById($airportId);

        // Load flights and return a count
        $airportService->getFlightGroup();
        return $airportService->getFlightGroupCount();
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  tags="TO-DO",
     *  description="Generate data to use in graphs"
     * )
     *
     * @Rest\View()
     */
    public function cgetGraphAction(Request $request, $airportId)
    {
        // Load service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set filters in service
        $airportService
            ->setParam('direction', $request->query->get('direction'))
            ->setParam('from_time', $request->query->get('from_time'))
            ->setParam('to_time', $request->query->get('to_time'));


        // Associate with current airport
        // and return graph data to client
        $airportService->setParentById($airportId);
        $airportService->getFlightGroup();
        return $airportService->getGraphData();
    }


    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Returns a flight object from a given airport",
     *  statusCodes={
     *      200="Returned when successful",
     *      404={
     *          "Returned when airport was not found",
     *          "Or when flight was not found"
     *      }
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"},
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"},
     *  }
     * )
     *
     * @Rest\View()
     */
    public function getAction($airportId, $flightId)
    {
        // Load service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Load flight by airport and flight identifier (or throw a 404 exception)
        return $airportService
            ->setFlightById($airportId, $flightId)
            ->getFlight();
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Creates a flight and returns the view.",
     *  statusCodes={
     *      201="Returned when flight was successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      403="Returned when the request was not authorized to create a flight",
     *      404="Returned when given airport was not found"
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"},
     *  }
     * )
     *
     * @return View|array
     */
    public function cpostAction(Request $request, $airportId)
    {
        // Load service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Associate with given airport
        $airportService->setParentById($airportId);

        // Get airport entity
        $airportEntity = $airportService->getParent();

        // Create a new form for this flight
        $flightEntity = new Flight();
        $flightEntity->setAirport($airportEntity);
        $form = $this->createForm(new FlightType(), $flightEntity);
        $form->bind($request);

        // Validate form request
        if ($form->isValid()) {
            // Save flight
            $em = $this->getDoctrine()->getManager();
            $em->persist($flightEntity);
            $em->flush();

            // Redirect and return the new flight view with "HTTP 201 Created"
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

        // Else return a form to create a new flight
        return array(
            'form' => $form
        );
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Quickcreate a flight with PUT",
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
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function putAction(Request $request, $airportId, $flightId)
    {
        // Load service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set flight in service
        $airportService->setFlightById($airportId, $flightId);

        // Get the flight entity and create a form instance
        $flightEntity = $airportService->getFlight();
        $form = $this->createForm(new FlightType(), $flightEntity);

        // Validate form request
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($flightEntity);
            $em->flush();

            // Return nothing when saved
            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        // Else return the form again
        return array(
            'form' => $form,
        );
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Deletes a flight from an airport.",
     *  statusCodes={
     *      204="Returned when flight was successfully deleted",
     *      403="Returned when the request was not authorized to delete the flight",
     *      404={
     *          "Returned when airport was not found",
     *          "Or when flight was not found"
     *      }
     *  },
     *  requirements={
     *      {"name"="airportId", "dataType"="integer", "requirement"="\d+", "description"="Unique airport identifier"},
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function deleteAction($airportId, $flightId)
    {
        // Load the service
        $airportService = $this->container->get('frigg_flight.airport_service');

        // Set airport flight in service (or throw an exception)
        $airportService->setFlightById($airportId, $flightId);

        // Delete the flight
        $flightEntity = $airportService->getFlight();
        $em = $this->getDoctrine()->getManager();
        $em->remove($flightEntity);
        $em->flush();

        // Return empty view with HTTP 204 ("No Content")
        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
