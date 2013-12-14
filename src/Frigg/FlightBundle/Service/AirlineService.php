<?php

namespace Frigg\FlightBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AirlineService extends FlightParentAbstract
{
    /**
     * Airline subclass constructor
     * @var EntityManager $em
     * @var string $config
     **/
    public function __construct(EntityManager $em, SessionInterface $session, $config)
    {
        parent::__construct($em, $session, $config);
    }

    /**
     * Get all airline entities
     * @return array
     **/
    public function getAll()
    {
        return $this->em->getRepository('FriggFlightBundle:Airline')->findAll();
    }

   /**
     * Get session entity from session
     * @return object
     **/
    public function getSessionEntity()
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airline')->find($this->getSession());

        if (!$entity) {
           throw new NotFoundHttpException('Unable to find airline entity');
        }

        $this->setParent($entity);
        return $entity;
    }

    /**
     * Set airline entity
     * @var integer $parentId
     * @return AirlineService
     **/
    public function setParentById($parentId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Airline')->find($parentId);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find airline entity');
        }

        $this->setParent($entity);
        return $this;
    }

    /**
     * Set new flight linked with this airline
     * @var integer $parentId
     * @var integer $flightId
     * @return AirlineService
     **/
    public function setFlightById($parentId, $flightId)
    {
        $entity = $this->em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'id' => $flightId,
                'airline' => $parentId,
            )
        );

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find flight entity');
        }

        $this->setFlight($entity);
        return $this;
    }

     /**
     * Fetch scheduled flights group for this airline
     * @return array
     **/
    protected function loadFlightGroup()
    {
        if (!$this->parentEntity) {
            throw new NotFoundHttpException('Unable to load flights. Airline missing.');
        }

        return $this->em->createQueryBuilder()->select('f')
            ->from('FriggFlightBundle:Flight', 'f')
            ->where('f.schedule_time >= :schedule_time')
            ->andWhere('f.airline = :airline')
            ->setParameter('schedule_time', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('airline', $this->parentEntity->getId())
            ->getQuery()
            ->getResult();
    }
}
