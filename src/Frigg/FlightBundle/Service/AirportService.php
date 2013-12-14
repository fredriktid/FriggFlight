<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AirportService extends FlightParentAbstract
{
    protected $params = array();

    /**
     * Airport subclass constructor
     * @var EntityManager $em
     * @var string $config
     **/
    public function __construct(EntityManager $em, SessionInterface $session, $config)
    {
        parent::__construct($em, $session, $config);
    }

    /**
     * Get all airport entities
     * @return array
     **/
    public function getAll()
    {
        return $this->em->getRepository('FriggFlightBundle:Airport')->findAll();
    }

   /**
     * Get session entity from session
     * @return object
     **/
    public function getSessionEntity()
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airport')->find($this->getSession());

        if (!$entity) {
           throw new NotFoundHttpException('Unable to find airport entity');
        }

        $this->setParent($entity);
        return $entity;
    }

    /**
     * Set airport entity
     * @var integer $parentId
     * @return AirportService
     **/
    public function setParentById($parentId)
    {
        $airportEntity = $this->em->getRepository('FriggFlightBundle:Airport')->find($parentId);

        if (!$airportEntity) {
            throw new NotFoundHttpException('Unable to find airport entity');
        }

        $this->setParent($airportEntity);
        return $this;
    }

    /**
     * Set flight entity in context of parent
     * @var integer $parentId
     * @var integer $flightId
     * @return AirportService
     **/
    public function setFlightById($parentId, $flightId)
    {
        $flightEntity = $this->em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airport' => $parentId,
            )
        );

        if (!$flightEntity) {
            throw new NotFoundHttpException('Unable to find flight entity');
        }

        $this->setFlight($flightEntity);
        return $this;
    }

    /**
     * Get all Avinor airports
     * @return array
     **/
    public function getAvinorAirports()
    {
        return $this->em->createQueryBuilder()->select('a')
            ->from('FriggFlightBundle:Airport', 'a')
            ->where('a.is_avinor = :is_avinor')
            ->setParameter('is_avinor', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch scheduled flights group for this airport
     * @return array
     **/
    protected function loadFlightGroup()
    {
        if (!$this->parentEntity) {
            throw new NotFoundHttpException('Unable to load flights. Missing parent entity.');
        }

        $direction = $this->getParam('direction');
        $isDelayed = ($this->getParam('is_delayed') == 'Y');
        $fromTime = ($this->getParam('from_time')) ? $this->getParam('from_time') : mktime(0, 0, 0);
        $toTime = ($this->getParam('to_time')) ? $this->getParam('to_time') : mktime(23, 59, 59);

        $qb = $this->em->createQueryBuilder();
        $query = $qb->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
           ->where($qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->gte('f.schedule_time', ':from_time'),
                    $qb->expr()->andX(
                        $qb->expr()->neq('f.flight_status_time', ':status_default'),
                        $qb->expr()->gte('f.flight_status_time', ':from_time')
                    )
                ),
                $qb->expr()->lte('f.schedule_time', ':to_time')
           ))
           ->andWhere('f.airport = :airport');

        if ($direction) {
            $query->andWhere('f.arr_dep = :direction');
            $query->setParameter('direction', $direction);
        }

        if ($isDelayed) {
            $query->andWhere('f.is_delayed = :is_delayed');
            $query->setParameter('is_delayed', $isDelayed);
        }

        $query->orderBy('f.schedule_time', 'ASC')
            ->setParameter('from_time', new \DateTime(date('Y-m-d H:i:s', (int) $fromTime)), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('to_time', new \DateTime(date('Y-m-d H:i:s', $toTime)), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airport', $this->parentEntity->getId())
            ->setParameter('status_default', NULL);

        return $query->getQuery()->getResult();
    }

    public function getGraphData()
    {
        /*$day = date('d');
        $month = date('m');
        $year = date('y');

        $flightDirectionMap = array(
            'departures' => 'D',
            'arrivals' => 'A'
        );

        $currentdays = intval(date('t'));
        $interval = array();

        $i = 0;
        while ($i++ < $currentdays) {
            $interval[$i] = $i;
        }

        $data = array();
        foreach($this->flightGroup as $i => $flight) {
            if($flightDirection = array_search($flight->getArrDep(), $flightDirectionMap)) {
                $flightInterval = $flight->getScheduleTime()->format('j');
                if (!isset($data[$flightDirection][$flightInterval])) {
                    $data[$flightDirection][$flightInterval] = 0;
                }
                $data[$flightDirection][$flightInterval]++;
            }
        }

        return $data + array('ticks' => $interval);
        */
        return array();
    }
}
