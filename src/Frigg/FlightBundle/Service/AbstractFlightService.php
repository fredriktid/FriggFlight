<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractFlightService
{
    protected $em;
    protected $config;
    protected $session;

    protected $parent = null;
    protected $entity = null;
    protected $group = array();
    protected $filters = array();

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
     * Fetch scheduled flights group for this context
     * @return array
     **/
    abstract public function loadGroup();

    /**
     * Set parent entity by Id in context
     * @var integer $parentId
     * @return AbstractFlightService
     **/
    abstract public function setParentById($parentId);

    /**
     * Set flight entity in context of parent
     * @var integer $parentId
     * @var integer $flightId
     * @return AbstractFlightService
     **/
    abstract public function setEntityById($parentId, $flightId);

    /**
     * Get session entity from session
     * @return mixed
     **/
    abstract public function getSessionEntity();

    /**
     * Set parent entity in context
     * @var mixed $parent
     * @return AbstractFlightService
     **/
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent entity in context
     * @return mixed
     **/
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get value from config
     * @var string $key
     * @return mixed
     **/
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return null;
    }

    /**
     * Get the default Id of the current parent
     * @return integer
     **/
    public function getDefaultParentId()
    {
        if (array_key_exists('default', $this->config)) {
            return $this->config['default'];
        }

        return null;
    }

    /**
     * Get a parameter value
     * @var string $key
     * @return mixed
     **/
    public function getFilter($key = null)
    {
        if ($key === null) {
            return $this->filters;
        }

        if (array_key_exists($key, $this->filters) {
            return $this->filters[$key];
        }

        return null;
    }

    /**
     * Set a parameter key
     * @var string $key
     * @var mixed $value
     * @return AbstractFlightService
     **/
    public function setFilter($key, $value = null)
    {
        if (is_array($key)) {
            $this->filters = $key;
            return $this;
        }

        $this->filters[$key] = $value;
        return $this;
    }

    /**
     * Get current flight in context
     * @return Flight
     **/
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set flight in context
     * @var Flight $entity
     * @return AbstractFlightService
     **/
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get all loaded flights
     * @return array
     **/
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set flights
     * @var array $group
     * @return AbstractFlightService
     **/
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Count loaded flights
     * @return integer
     **/
    public function countGroup()
    {
        return count($this->group);
    }

    /**
     * Get session data in this context
     * @return mixed
     **/
    public function sessionValue()
    {
        return $this->session->get(get_called_class());
    }

    /**
     * Append (or prepend) to session data in this context
     * @var mixed $value
     * @return AbstractFlightService
     **/
    public function appendSession($value, $clear = false, $prependInstead = false)
    {
        $sessionValue = $this->sessionValue();

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
     * @return AbstractFlightService
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
                'data' => 'sessionValue',
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
            $currentSession = $this->sessionValue();
            if ($sessionValue !== $currentSession) {
                $this->session->set(get_called_class(), $sessionValue);
            }

            return true;
        }

        return false;
    }
}
