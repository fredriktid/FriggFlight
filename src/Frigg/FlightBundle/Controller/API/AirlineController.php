<?php

namespace Frigg\FlightBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Form\AirlineType;


class AirlineController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @ApiDoc(
     *  section="Airlines",
     *  resource=true,
     *  description="Returns a collection of all airlines",
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
        return $em->getRepository('FriggFlightBundle:Airline')->findAll();
    }

    /**
     * @ApiDoc(
     *  section="Airlines",
     *  resource=true,
     *  description="Returns a single airline object",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when airline does not exist"
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"},
     *  }
     * )
     *
     * @Rest\View()
     */
    public function getAction($airlineId)
    {
        $airlineService = $this->container->get('frigg.airline.flight');
        $airlineService->setParentById($airlineId);
        return $airlineService->getParent();
    }

    /**
     * @ApiDoc(
     *  section="Airlines",
     *  tags="TO-DO",
     *  description="Generates and returns graph data"
     * )
     *
     * @Rest\View()
     */
    public function cgetGraphAction(Request $request, $airlineId)
    {
        return null;
    }

    /**
     * @ApiDoc(
     *  section="Airlines",
     *  resource=true,
     *  description="Creates an airline and returns the view",
     *  statusCodes={
     *      201="Returned when airline was successfully created",
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
            throw new AccessDeniedHttpException('Access denied. Only administrators may add data to the API.');
        }

        // Create Airline form instance based
        $airlineEntity = new Airline();
        $form = $this->createForm(new AirlineType(), $airlineEntity);

        // Validate against request data
        $form->bind($request);
        if ($form->isValid()) {
            // Save new airline entity
            $em = $this->getDoctrine()->getManager();
            $em->persist($airlineEntity);
            $em->flush();

            // Return view of new airline entity (HTTP 201)
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

        // Else return form to client
        return array(
            'form' => $form,
        );
    }

    /**
     * @ApiDoc(
     *  section="Airlines",
     *  resource=true,
     *  description="Quickcreates an airline",
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
     *  }
     * )
     *
     * @return View|array
     */
    public function putAction(Request $request, $airlineId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied. Only administrators may add data to the API.');
        }

        // Load service
        $airlineService = $this->container->get('frigg.airline.flight');

        // Create a form instance of given airline
        $airlineService->setParentById($airlineId);
        $airlineEntity = $airlineService->getParent();
        $form = $this->createForm(new AirlineType(), $airlineEntity);

        // Validate request data with form
        $form->bind($request);
        if ($form->isValid()) {
            // Create new airline
            $em = $this->getDoctrine()->getManager();
            $em->persist($airlineEntity);
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
     *  section="Airlines",
     *  resource=true,
     *  description="Delete an airline",
     *  statusCodes={
     *      204="Returned when airline was successfully deleted",
     *      403="Returned when the request was not authorized",
     *      404="Returned when airline does not exist"
     *  },
     *  requirements={
     *      {"name"="airlineId", "dataType"="integer", "requirement"="\d+", "description"="Unique airline identifier"}
     *  }
     * )
     *
     * @return View|array
     */
    public function deleteAction($airlineId)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Access denied. Only administrators may delete data from the API');
        }

        // Load service
        $airlineService = $this->container->get('frigg.airline.flight');

        // Fetch given airline (or throw 404 exception here)
        $airlineService->setParentById($airlineId);
        $airlineEntity = $airlineService->getParent();

        // Delete airline entity
        $em = $this->getDoctrine()->getManager();
        $em->remove($airlineEntity);
        $em->flush();

        // Return empty view (HTTP 204)
        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
