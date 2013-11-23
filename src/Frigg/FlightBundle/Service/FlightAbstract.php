<?php

namespace Frigg\FlightBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airport;

abstract class FlightAbstract
{
    protected $config;
    protected $container;
    protected $em;

    protected $entity = null;

    public function __construct(ContainerInterface $container, $config)
    {
        $this->config = $config;
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        if (is_null($this->entity)) {
            return $this->getDefault();
        }

        return $this->entity;
    }

    abstract protected function getDefault();

    abstract public function getScheduledFlights();

}
