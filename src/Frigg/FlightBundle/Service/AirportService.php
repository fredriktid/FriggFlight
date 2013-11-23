<?php

namespace Frigg\FlightBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AirportService extends FlightAbstract
{

    public function __construct(ContainerInterface $container, $config)
    {
        parent::__construct($container, $config);
    }

    protected function getDefault()
    {
        $defaultId = (isset($this->config['default'])) ? $this->config['default'] : 0;

        if ($defaultEntity = $this->em->getRepository('FriggFlightBundle:Airport')->find($defaultId)) {
            return $defaultEntity;
        }

        return false;
    }

    public function getAvinorAirports()
    {
        return $this->em->createQueryBuilder()->select('a')
            ->from('FriggFlightBundle:Airport', 'a')
            ->where('a.is_avinor = :is_avinor')
            ->setParameter('is_avinor', true)
            ->getQuery()
            ->getResult();
    }

    public function getScheduledFlights()
    {
        if (is_null($this->entity)) {
            return array();
        }

        return $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airport = :airport')
            ->orderBy('f.schedule_time', 'ASC')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airport', $this->entity->getId())
            ->getQuery()
            ->getResult();
    }
}
