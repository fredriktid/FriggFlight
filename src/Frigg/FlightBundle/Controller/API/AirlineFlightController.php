<?php

namespace Frigg\FlightBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Form\FlightType;

class AirlineFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @ApiDoc(
     *  section="Airline flights",
     *  resource=true,
     *  description="Returns a group of flights from an airline",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when airline does not exist"
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"}
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
    public function cgetAction(Request $request, $airlineId)
    {
        // Load service
        $airlineService = $this->container->get('frigg.airline.flight');

        // Set filters in service
        $airlineService
            ->setFilter('direction', $request->query->get('direction'))
            ->setFilter('from_time', $request->query->get('from_time'))
            ->setFilter('to_time', $request->query->get('to_time'))
            ->setFilter('is_delayed', $request->query->get('is_delayed'));

        // Associate with given airline
        $airlineService->setParentById($airlineId);

        // Load group collection
        $airlineService->loadGroup();

        // Return group of flights
        return $airlineService->getGroup();
    }

    /**
     * @ApiDoc(
     *  section="Airline flights",
     *  resource=true,
     *  description="Returns a flight object from a given airline",
     *  statusCodes={
     *      200="Returned when successful",
     *      404={
     *          "Returned when airline does not exist",
     *          "Or when flight does not exist"
     *      }
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"},
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"},
     *  }
     * )
     *
     * @Rest\View()
     */
    public function getAction($airlineId, $flightId)
    {
        $airlineService = $this->container->get('frigg.airline.flight');
        $airlineService->setEntityById($airlineId, $flightId);
        return $airlineService->getEntity();
    }

    /**
     * @ApiDoc(
     *  section="Airline flights",
     *  resource=true,
     *  description="Counts a group of flights from an airline",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the airline does not exist"
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"}
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
    public function cgetCountAction(Request $request, $airlineId)
    {
        $airlineFlightGroup = $this->cgetAction($request, $airlineId);
        return count($airlineFlightGroup);
    }

    /**
     * @ApiDoc(
     *  section="Airline flights",
     *  resource=true,
     *  description="Creates an airline flight and returns the view",
     *  statusCodes={
     *      201="Returned when flight was successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      403="Returned when the request was not authorized",
     *      404="Returned when given airline does not exist"
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"},
     *  }
     * )
     *
     * @return View|array
     */
    public function cpostAction(Request $request, $airlineId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators may add data to the API.');
        }

        // Load service
        $airlineService = $this->container->get('frigg.airline.flight');

        // Associate with airline
        $airlineService->setParentById($airlineId);

        // Get airline entity
        $airlineEntity = $airlineService->getParent();

        // Create form instance from airline entity
        $entity = new Flight();
        $entity->setAirline($airlineEntity);
        $form = $this->createForm(new FlightType(), $entity);

        // Validate received data against form
        $form->bind($request);
        if ($form->isValid()) {
            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // Return view of new entity (HTTP 201)
            return $this->redirectView(
                $this->generateUrl(
                    'get_airline_flight',
                    array(
                        'airlineId' => $entity->getAirline()->getId(),
                        'flightId' => $entity->getId()
                    )
                ),
                Codes::HTTP_CREATED
            );
        }

        // Else return form to client
        return array(
            'form' => $form,
        );
    }

    /**
     * @ApiDoc(
     *  section="Airline flights",
     *  resource=true,
     *  description="Quickcreates an airline flight",
     *  statusCodes={
     *      204="Returned when the flight has been successfully created",
     *      200="Returned when request did not validate and the form is returned",
     *      404={
     *          "Returned when airline does not exist",
     *          "Or when flight does not exist"
     *      }
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"},
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function putAction(Request $request, $airlineId, $flightId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators may add data to the API');
        }

        // Load instance and associate with given airline
        $airlineService = $this->container->get('frigg.airline.flight');
        $airlineService->setEntityById($airlineId, $flightId);

        // Create form by airline entity
        $entity = $airlineService->getEntity();
        $form = $this->createForm(new FlightType(), $entity);

        // Validate request data against form
        $form->bind($request);
        if ($form->isValid()) {
            // Create new entity
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // Return empty view (HTTP 204)
            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        // Else return view to client
        return array(
            'form' => $form,
        );
    }

    /**
     * @ApiDoc(
     *  section="Airline flights",
     *  resource=true,
     *  description="Delete a flight from an airline",
     *  statusCodes={
     *      204="Returned when flight was successfully deleted",
     *      403="Returned when the request was not authorized",
     *      404={
     *          "Returned when airline does not exist",
     *          "Or when flight does not exist"
     *      }
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"},
     *      {"name"="flightId", "dataType"="integer", "requirement"="\d+", "description"="Unique flight identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function deleteAction($airlineId, $flightId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators may delete data from the API');
        }

        // Load service
        $airlineService = $this->container->get('frigg.airline.flight');

        // Set airline in service
        $airlineService->setEntityById($airlineId, $flightId);

        // Delete airline flight entity
        $entity = $airlineService->getEntity();
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        // Return empty view (HTTP 204)
        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
