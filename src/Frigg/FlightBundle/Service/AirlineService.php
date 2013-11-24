<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AirlineService extends FlightParentAbstract
{
    /**
     * Subclass constructor
     * @var EntityManager $em
     * @var string $configFile
     **/
    public function __construct(EntityManager $em, SessionInterface $session, $config)
    {
        parent::__construct($em, $session, $config);
    }

    /**
     * Get all parent entities
     * @return array
     **/
    public function getAll()
    {
        return $this->em->getRepository('FriggFlightBundle:Airline')->findAll();
    }

    /**
     * Set airline entity by Id
     * @var integer $entityId Id of airline to fetch
     * @return AirlineService
     **/
    public function setEntityById($entityId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airline')->find($entityId);

        if (!$entity) {
            throw new \Exception('Unable to find airline entity');
        }

        $this->setEntity($entity);
        return $this;
    }

    /**
     * Set new flight entity linked with current airline
     * @var integer $entityId
     * @var integer $flightId
     * @return AirlineService
     **/
    public function setFlightById($entityId, $flightId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airline' => $entityId,
            )
        );

        if (!$entity) {
            throw new \Exception('Unable to find flight entity');
        }

        $this->setFlight($entity);
        return $this;
    }

    /**
     * Fetch scheduled flights from current airline
     * @return array
     **/
    public function getFlights()
    {
        if (!$this->entity) {
            throw new \Exception('Missing airline entity in service');
        }

        if (!$this->flights) {
            $this->setFlights($this->em->createQueryBuilder()->select('f')
                ->from('FriggFlightBundle:Flight', 'f')
                ->where('f.schedule_time >= :schedule_time')
                ->andWhere('f.airline = :airline')
                ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('airline', $this->entity->getId())
                ->getQuery()
                ->getResult()
            );
        }

        return $this->getFlights();
    }
}
