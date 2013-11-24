<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;

class AirlineService extends FlightAbstract
{
    /**
     * Subclass constructor
     * @var EntityManager $entityManager Doctrine entity manger
     * @var array $configFile Airline configuration file
     **/
    public function __construct(EntityManager $entityManager, $configFile)
    {
        parent::__construct($entityManager, $configFile);
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
     * Set parent entity by Id in instance
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
     * Set new flight entity in instance
     * @var integer $entityId Id of airline to fetch
     * @var integer $flightId Id of flight to fetch
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
     * Fetch scheduled flights from parent entity
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
