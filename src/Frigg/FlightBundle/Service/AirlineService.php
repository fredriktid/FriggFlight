<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;

class AirlineService extends FlightAbstract
{
    public function __construct(EntityManager $entityManager, $config)
    {
        parent::__construct($entityManager, $config);
    }

    public function getAll()
    {
        return $this->em->getRepository('FriggFlightBundle:Airline')->findAll();
    }

    public function setEntityById($entityId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airline')->find($entityId);

        if (!$entity) {
            throw new \Exception('Unable to find airline entity');
        }

        $this->entity = $entity;
    }

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

        $this->flight = $entity;
    }

    protected function getDefaultEntity()
    {
        $defaultId = (isset($this->config['default'])) ? $this->config['default'] : 0;

        if (!$defaultEntity = $this->em->getRepository('FriggFlightBundle:Airline')->find($defaultId)) {
            throw new \Exception('Unable to find default airline entity');
        }

        return $defaultEntity;
    }

    public function getData()
    {
        if (!$this->entity) {
            throw new \Exception('Missing airline entity in service');
        }

        $this->data = $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airline = :airline')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airline', $this->entity->getId())
            ->getQuery()
            ->getResult();

        return $this->data;
    }
}
