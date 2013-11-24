<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;

abstract class FlightAbstract
{
    protected $em;
    protected $config;

    protected $entity = null; // parent entity
    protected $flight = null; // flight entity
    protected $data = array(); // collection of flights

    public function __construct(EntityManager $entityManager, $config)
    {
        $this->em = $entityManager;
        $this->config = $config;
    }

    abstract public function getAll();

    abstract public function setEntityById($entityId);

    abstract public function setFlightById($entityId, $flightId);

    abstract protected function getDefaultEntity();

    abstract public function getData();

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        if (is_null($this->entity)) {
            return $this->getDefaultEntity();
        }

        return $this->entity;
    }

    public function setFlight($flight)
    {
        $this->flight = $flight;
    }

    public function getFlight()
    {
        return $this->flight;
    }

    public function getDataCount()
    {
        return count($this->data);
    }
}
