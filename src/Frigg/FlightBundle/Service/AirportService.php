<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AirportService extends FlightParentAbstract
{
    /**
     * Airport subclass constructor
     * @var EntityManager $em
     * @var string $config
     **/
    public function __construct(EntityManager $em, SessionInterface $session, $config)
    {
        parent::__construct($em, $session, $config);
    }

    /**
     * Get all airport entities
     * @return array
     **/
    public function getAll()
    {
        return $this->em->getRepository('FriggFlightBundle:Airport')->findAll();
    }

    /**
     * Set airport entity
     * @var integer $parentId
     * @return AirportService
     **/
    public function setParentById($parentId)
    {
        $airportEntity = $this->em->getRepository('FriggFlightBundle:Airport')->find($parentId);

        if (!$airportEntity) {
            throw new NotFoundHttpException('Unable to find airport entity');
        }

        $this->setParent($airportEntity);
        return $this;
    }

    /**
     * Set flight entity in context of parent
     * @var integer $parentId
     * @var integer $flightId
     * @return AirportService
     **/
    public function setFlightById($parentId, $flightId)
    {
        $flightEntity = $this->em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airport' => $parentId,
            )
        );

        if (!$flightEntity) {
            throw new NotFoundHttpException('Unable to find flight entity');
        }

        $this->setFlight($flightEntity);
        return $this;
    }

    /**
     * Get all Avinor airports
     * @return array
     **/
    public function getAvinorAirports()
    {
        return $this->em->createQueryBuilder()->select('a')
            ->from('FriggFlightBundle:Airport', 'a')
            ->where('a.is_avinor = :is_avinor')
            ->setParameter('is_avinor', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch scheduled flights group for this airport
     * @return array
     **/
    protected function loadFlightsGroup()
    {
        if (!$this->parentEntity) {
            throw new NotFoundHttpException('Unable to load flights. Missing parent entity.');
        }

        return $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airport = :airport')
            ->orderBy('f.schedule_time', 'ASC')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airport', $this->parentEntity->getId())
            ->getQuery()
            ->getResult();
    }
}
