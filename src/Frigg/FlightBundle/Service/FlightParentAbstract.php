<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class FlightParentAbstract
{
    /* Injected dependencies */
    protected $em;
    protected $config;
    protected $session;

    /* Parent entity */
    protected $parent = null;

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
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the default Id of the current parent
     * @return integer
     **/
    public function getDefaultParentId()
    {
        return (isset($this->config['default']) ? $this->config['default'] : null);
    }

    /**
     * Set parent entity in context
     * @var mixed $entity
     * @return FlightParentAbstract
     **/
    public function setParent($entity)
    {
        $this->parent = $entity;
        return $this;
    }

    /**
     * Set parent entity by Id in context
     * @var integer $parentId
     * @return FlightParentAbstract
     **/
    abstract public function setParentById($parentId);

    /**
     * Get current flight in context
     * @return Flight
     **/
    public function getFlight()
    {
        return $this->flight;
    }

    /**
     * Set flight in context
     * @var Flight $flight
     * @return FlightParentAbstract
     **/
    public function setFlight($flight)
    {
        $this->flight = $flight;
        return $this;
    }

    /**w
     * Get all loaded flights
     * @return array
     **/
    public function getFlights()
    {
        if (!$this->parent) {
            throw new NotFoundHttpException('Unable to get flights. Missing parent entity.');
        }

        if (!$this->flights) {
            $this->flights = $this->loadFlights();
        }

        return $this->flights;
    }

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
     * Set new flight linked with current parent
     * @var integer $parentId
     * @var integer $flightId
     * @return FlightParentAbstract
     **/
    abstract public function setFlightById($parentId, $flightId);

    /**
     * Fetch scheduled flights for this context
     * @return array
     **/
    abstract protected function loadFlights();

    /**
     * Count loaded flights
     * @return integer
     **/
    public function getFlightsCount()
    {
        return count($this->flights);
    }

    /**
     * Get session data in this context
     * @return mixed
     **/
    public function getSession()
    {
        return $this->session->get(get_called_class());
    }

    /**
     * Append (or prepend) to session data in this context
     * @var mixed $value
     * @return FlightParentAbstract
     **/
    public function appendSession($value, $clear = false, $prependInstead = false)
    {
        $sessionValue = $this->getSession();

        if (is_null($value) || $clear) {
            $sessionValue = array();
        }

        if (!is_array($sessionValue)) {
            $sessionValue = array($sessionValue);
        }

        if ($prependInstead) {
            array_unshift($sessionValue, $value);
        } else {
            array_push($sessionValue, $value);
        }

        $this->setSession($sessionValue);

        return $this;
    }

    /**
     * Set session data in this context
     * @var mixed $value
     * @var bool $defaultToEntityId
     * @return FlightParentAbstract
     **/
    public function setSession($value, $defaultToEntityId = false)
    {
        $sessionValue = null;
        $fallbackList = array(
            array(
                'type' => 'variable',
                'data' => $value,
                'params' => null
            ),
            array(
                'type' => 'function',
                'data' => 'getSession',
                'params' => array()
            )
        );

        foreach ($fallbackList as $i => $data) {
            if (!is_null($sessionValue)) {
                break;
            }

            switch ($data['type']) {
                case 'variable':
                    $sessionValue = $data['data'];
                    break;
                case 'function':
                    $sessionValue = call_user_func_array(array($this, $data['data']), $data['params']);
                    break;
            }
        }

        if (is_null($sessionValue) && $defaultToEntityId) {
            $sessionValue = $this->getDefaultParentId();
        }

        if (!is_null($sessionValue)) {
            $currentSession = $this->getSession();
            if ($sessionValue !== $currentSession) {
                $this->session->set(get_called_class(), $sessionValue);
            }

            return true;
        }

        return false;
    }
}
