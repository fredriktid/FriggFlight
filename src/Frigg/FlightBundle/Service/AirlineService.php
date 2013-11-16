<?php

namespace Frigg\FlightBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Entity\Airport;

class AirlineService
{
    protected $container;
    protected $em;

    protected $airline = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function setAirline(Airline $airline)
    {
        $this->airline = $airline;
    }

    public function getScheduledFlights()
    {
        if (is_null($this->airline)) {
            return array();
        }

        return $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airline = :airline')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airline', $this->airline->getId())
            ->getQuery()
            ->getResult();
    }
}
