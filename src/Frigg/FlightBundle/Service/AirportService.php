<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;

class AirportService extends FlightAbstract
{
    /**
     * Subclass constructor
     * @var EntityManager $entityManager Doctrine entity manager
     * @var array $configFile Airport configuration file
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
        return $this->em->getRepository('FriggFlightBundle:Airport')->findAll();
    }

    /**
     * Set parent entity by Id in instance
     * @var integer $entityId Id of airport to fetch
     * @return AirportService
     **/
    public function setEntityById($entityId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airport')->find($entityId);

        if (!$entity) {
            throw new \Exception('Unable to find airport entity');
        }

        $this->setEntity($entity);
        return $this;
    }

    /**
     * Set new flight entity in instance
     * @var integer $entityId Id of airport to fetch
     * @var integer $flightId Id of flight to fetch
     * @return AirportService
     **/
    public function setFlightById($entityId, $flightId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airport' => $entityId,
            )
        );

        if (!$entity) {
            throw new \Exception('Unable to find flight entity');
        }

        $this->setFlight($entity);
        return $this;
    }

    /**
     * Get list of Avinor airports
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
     * Fetch scheduled flights from parent entity
     * @return array
     **/
    public function getFlights()
    {
        if (!$this->entity) {
            throw new \Exception('Missing airport entity in service');
        }

        if (!$this->flights) {
            $this->flights = $this->em->createQueryBuilder()->select('f')
                ->from('FriggFlightBundle:Flight', 'f')
                ->where('f.schedule_time >= :schedule_time')
                ->andWhere('f.airport = :airport')
                ->orderBy('f.schedule_time', 'ASC')
                ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('airport', $this->entity->getId())
                ->getQuery()
                ->getResult();
        }

        return $this->flights;
    }
}
