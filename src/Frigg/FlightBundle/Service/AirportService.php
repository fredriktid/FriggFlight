<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;

class AirportService extends FlightAbstract
{
    public function __construct(EntityManager $entityManager, $config)
    {
        parent::__construct($entityManager, $config);
    }

    public function getAll()
    {
        return $this->em->getRepository('FriggFlightBundle:Airport')->findAll();
    }

    public function setEntityById($entityId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airport')->find($entityId);

        if (!$entity) {
            throw new \Exception('Unable to find airport entity');
        }

        $this->entity = $entity;
    }

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

        $this->flight = $entity;
    }

    protected function getDefaultEntity()
    {
        $defaultId = (isset($this->config['default'])) ? $this->config['default'] : 0;

        if (!$defaultEntity = $this->em->getRepository('FriggFlightBundle:Airport')->find($defaultId)) {
            throw new \Exception('Unable to find default airport entity');
        }

        return $defaultEntity;
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

    public function getData()
    {
        if (!$this->entity) {
            throw new \Exception('Missing airport entity in service');
        }

        $this->data = $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airport = :airport')
            ->orderBy('f.schedule_time', 'ASC')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airport', $this->entity->getId())
            ->getQuery()
            ->getResult();

        return $this->data;
    }
}
