<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class FlightParentAbstract
{
    protected $em;
    protected $config;
    protected $session;

    protected $parentEntity = null;
    protected $flightEntity = null;
    protected $flightGroup = array();
    protected $params = array();

    /**
     * Flight parent constructor
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
    final public function getParent()
    {
        return $this->parentEntity;
    }

    /**
     * Get value from config
     * @var string $key
     * @return mixed
     **/
    final public function getConfig($key)
    {
        return (isset($this->config[$key])) ? $this->config[$key] : null;
    }

    /**
     * Get the default Id of the current parent
     * @return integer
     **/
    final public function getDefaultParentId()
    {
        return (isset($this->config['default']) ? $this->config['default'] : null);
    }

    /**
     * Set parent entity in context
     * @var mixed $parent
     * @return FlightParentAbstract
     **/
    final public function setParent($parentEntity)
    {
        $this->parentEntity = $parentEntity;
        return $this;
    }

    /**
     * Set parent entity by Id in context
     * @var integer $parentId
     * @return FlightParentAbstract
     **/
    abstract public function setParentById($parentId);

    /**
     * Get a parameter value
     * @var string $key
     * @return mixed
     **/
    public function getParam($key)
    {
        return (isset($this->params[$key])) ? $this->params[$key] : null;
    }

    /**
     * Set a parameter key
     * @var string $key
     * @var mixed $value
     * @return FlightParentAbstract
     **/
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Get current flight in context
     * @return Flight
     **/
    final public function getFlight()
    {
        return $this->flightEntity;
    }

    /**
     * Set flight in context
     * @var Flight $flightEntity
     * @return FlightParentAbstract
     **/
    final public function setFlight($flightEntity)
    {
        $this->flightEntity = $flightEntity;
        return $this;
    }

    /**
     * Set flight entity in context of parent
     * @var integer $parentId
     * @var integer $flightId
     * @return FlightParentAbstract
     **/
    abstract public function setFlightById($parentId, $flightId);

    /**
     * Get all loaded flights
     * @return array
     **/
    final public function getFlightGroup()
    {
        if (!$this->parentEntity) {
            throw new NotFoundHttpException('Unable to get flights. Missing parent entity.');
        }

        //if (!count($this->flightGroup)) {
            $this->flightGroup = $this->loadFlightGroup();
        //}

        return $this->flightGroup;
    }

    /**
     * Set flights
     * @var array $flightGroup
     * @return FlightParentAbstract
     **/
    final public function setFlightGroup($flightGroup)
    {
        $this->flightGroup = $flightGroup;
        return $this;
    }

    /**
     * Fetch scheduled flights group for this context
     * @return array
     **/
    abstract protected function loadFlightGroup();

    /**
     * Count loaded flights
     * @return integer
     **/
    public function getFlightGroupCount()
    {
        return count($this->flightGroup);
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
     * Get session entity from session
     * @return mixed
     **/
    abstract public function getSessionEntity();

    /**
     * Append (or prepend) to session data in this context
     * @var mixed $value
     * @return FlightParentAbstract
     **/
    public function appendSession($value, $clear = false, $prependInstead = false)
    {
        $sessionValue = $this->getSession();

        if ($clear || is_null($sessionValue)) {
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
