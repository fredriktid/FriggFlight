<?php

namespace Frigg\FlightBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AirlineService extends FlightAbstract
{

    public function __construct(ContainerInterface $container, $config)
    {
        parent::__construct($container, $config);
    }

    protected function getDefault()
    {
        $defaultId = (isset($this->config['default'])) ? $this->config['default'] : 0;

        if ($defaultEntity = $this->em->getRepository('FriggFlightBundle:Airline')->find($defaultId)) {
            return $defaultEntity;
        }

        return false;
    }

    public function getScheduledFlights()
    {
        if (is_null($this->entity)) {
            return array();
        }

        return $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airline = :airline')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airline', $this->entity->getId())
            ->getQuery()
            ->getResult();
    }
}
