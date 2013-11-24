<?php

namespace Frigg\FlightBundle\Service;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;

abstract class FlightAbstract
{
    /* Injected dependencies */
    protected $em;
    protected $config;

    /* Parent entity */
    protected $entity = null;

    /* Single flight entity */
    protected $flight = null;

    /* Collection of flights */
    protected $flights = array();

    /**
     * Parent constructor
     * @var EntityManager $entityManager Doctrine entity manger
     * @var array $configFile Parent entity configuration file
     **/
    public function __construct(EntityManager $entityManager, $configFile)
    {
        $this->em = $entityManager;
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    /**
     * Get all parent entities
     * @return array
     **/
    abstract public function getAll();

    /**
     * Get parent entity instance
     * @return object
     **/
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set parent entity in instance
     * @var object $entity Parent entity of flights
     * @return FlightAbstract
     **/
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Set parent entity by Id in instance
     * @var integer $entityId Id of airport to fetch
     * @return FlightAbstract
     **/
    abstract public function setEntityById($entityId);

    /**
     * Get the default Id of the current entity type
     * @return integer
     **/
    public function getDefaultEntityId()
    {
        return (isset($this->config['default']) ? $this->config['default'] : 0);
    }

    /**
     * Get current flight entity
     * @return Flight
     **/
    public function getFlight()
    {
        return $this->flight;
    }

    /**
     * Set flight entity in instance
     * @var Flight $flight
     * @return FlightAbstract
     **/
    public function setFlight($flight)
    {
        $this->flight = $flight;
        return $this;
    }

    /**
     * Set new flight entity in instance
     * @var integer $entityId
     * @var integer $flightId
     * @return FlightAbstract
     **/
    abstract public function setFlightById($entityId, $flightId);

    /**
     * Fetch scheduled flights from parent entity
     * @return array
     **/
    abstract public function getFlights();

    /**
     * Count all flights loaded in current instance
     * @return integer
     **/
    public function getFlightsCount()
    {
        return count($this->flights);
    }
}
