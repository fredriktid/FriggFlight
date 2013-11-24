<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class FlightParentAbstract
{
    /* Injected dependencies */
    protected $em;
    protected $config;
    protected $session;

    /* Parent entity */
    protected $entity = null;

    /* Single flight entity */
    protected $flight = null;

    /* Collection of flights */
    protected $flights = array();

    /**
     * Parent constructor
     * @var EntityManager $em
     * @var SessionInterface $session
     * @var string $config
     **/
    public function __construct(EntityManager $em, SessionInterface $session, $config)
    {
        $this->em = $em;
        $this->session = $session;
        $this->config = Yaml::parse(file_get_contents($config));
    }

    /**
     * Get all parent entities
     * @return array
     **/
    abstract public function getAll();

    /**
     * Get parent entity in context
     * @return mixed
     **/
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set parent entity in context
     * @var mixed $entity
     * @return FlightParentAbstract
     **/
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Set parent entity by Id in context
     * @var integer $entityId
     * @return FlightParentAbstract
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
     * Get current flight entity in context
     * @return Flight
     **/
    public function getFlight()
    {
        return $this->flight;
    }

    /**
     * Set flight entity in context
     * @var Flight $flight
     * @return FlightParentAbstract
     **/
    public function setFlight($flight)
    {
        $this->flight = $flight;
        return $this;
    }

    /**
     * Set new flight entity linked with current parent
     * @var integer $entityId
     * @var integer $flightId
     * @return FlightParentAbstract
     **/
    abstract public function setFlightById($entityId, $flightId);

    /**
     * Fetch scheduled flights from parent entity
     * @return array
     **/
    abstract public function getFlights();

    /**
     * Set new flights to instance
     * @var array $flights
     * @return FlightParentAbstract
     **/
    public function setFlights($flights)
    {
        $this->flights = $flights;
        return $this;
    }

    /**
     * Count all flights loaded in current instance
     * @return integer
     **/
    public function getFlightsCount()
    {
        return count($this->flights);
    }

    /**
     * Get value from associated session key
     * @return mixed
     **/
    public function getSession()
    {
        return $this->session->get(get_called_class());
    }

    /**
     * Append a value to user session
     * @var mixed $value
     * @return FlightParentAbstract
     **/
    public function appendSession($value)
    {
        $currentValue = $this->getSession();
        if (!$this->isValidSessionValue($currentValue)) {
            $currentValue = array();
        } elseif (!is_array($currentValue)) {
            $currentValue = array($currentValue);
        }

        $currentValue[] = $value;
        $this->setSession($currentValue);
        return $this;
    }

    /**
     * Sets value in associated session key
     * @var mixed $value
     * @var bool $defaultToEntityId
     * @return FlightParentAbstract
     **/
    public function setSession($value, $defaultToEntityId = false)
    {
        if (!$this->isValidSessionValue($value)) {
            $value = $this->getSession();
            if ($defaultToEntityId) {
                if (!$this->isValidSessionValue($value)) {
                    $value = $this->getDefaultEntityId();
                }
            }
        }

        $this->session->set(get_called_class(), $value);
        return $this;
    }

    /**
     * Validate session value, may be 0 but not null/false/blank etc
     * @var mixed $value
     * @return bool
     **/
    protected function isValidSessionValue($value)
    {
        return ($value || $value === 0);
    }
}
