<?php

namespace Frigg\FlightBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Form\FlightType;

class AirportFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Returns a group of flights from an airport",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when airport does not exist"
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
        $airportService = $this->container->get('frigg.airport.flight');

        // Set filters in service
        $airportService
            ->setFilter('direction', $request->query->get('direction'))
            ->setFilter('from_time', $request->query->get('from_time'))
            ->setFilter('to_time', $request->query->get('to_time'))
            ->setFilter('is_delayed', $request->query->get('is_delayed'));

        // Associate with current airport and load flights within filters
        $airportService->setParentById($airportId);
        $airportService->loadGroup();
        return $airportService->getGroup();
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
     *  resource=true,
     *  description="Counts a group of flights from an airport",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the airport does not exist"
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
        $airportFlightGroup = $this->cgetAction($request, $airportId);
        return count($airportFlightGroup);
    }

    /**
     * @ApiDoc(
     *  section="Airport flights",
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
     *  section="Airport flights",
     *  resource=true,
     *  description="Creates an airport flight and returns the view",
     *  statusCodes={
     *      201="Returned when flight was successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      403="Returned when the request was not authorized",
     *      404="Returned when given airport does not exist"
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied. Only administrators may add data to the API.');
        }

        // Load service
        $airportService = $this->container->get('frigg.airport.flight');

        // Associate with given airport
        $airportService->setParentById($airportId);

        // Get airport entity
        $airportEntity = $airportService->getParent();

        // Create a new form for this flight
        $entity = new Flight();
        $entity->setAirport($airportEntity);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        // Validate form request
        if ($form->isValid()) {
            // Save flight
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // Redirect and return the new flight view with "HTTP 201 Created"
            return $this->redirectView(
                $this->generateUrl(
                    'get_airport_flight',
                    array(
                        'airportId' => $entity->getAirport()->getId(),
                        'flightId' => $entity->getId()
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
     *  description="Quickcreates an airport flight",
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
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function putAction(Request $request, $airportId, $flightId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied. Only administrators may add data to the API.');
        }

        // Load service
        $airportService = $this->container->get('frigg.airport.flight');

        // Set flight in service
        $airportService->setEntityById($airportId, $flightId);

        // Get the flight entity and create a form instance
        $entity = $airportService->getEntity();
        $form = $this->createForm(new FlightType(), $entity);

        // Validate form request
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
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
     *  description="Delete a flight from an airport",
     *  statusCodes={
     *      204="Returned when flight was successfully deleted",
     *      403="Returned when the request was not authorized",
     *      404={
     *          "Returned when airport does not exist",
     *          "Or when flight does not exist"
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied. Only administrators may delete data from the API');
        }

        // Load the service
        $airportService = $this->container->get('frigg.airport.flight');

        // Set airport flight in service (or throw an exception)
        $airportService->setEntityById($airportId, $flightId);

        // Delete airport flight entity
        $entity = $airportService->getEntity();
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        // Return empty view with HTTP 204 ("No Content")
        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
