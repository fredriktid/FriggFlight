<?php

namespace Frigg\FlightBundle\Import;

use Frigg\FlightBundle\Entity\LastUpdated;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AvinorClient
{
    protected $em = null;
    protected $container = null;
    protected $data = array();

    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->container = $container;
    }

    abstract public function output();
    abstract public function run();

    protected function request($target)
    {
        printf('Request %s%s', $target, PHP_EOL);
        if (($content = file_get_contents($target, 'r')) !== false) {
            try {
                $this->data = simplexml_load_string($content);
                return $this->data;
            } catch(\Exception $e) {
                printf('Error: %s%s', $e->getMessage(), PHP_EOL);
            }
        }

        return false;
    }

    protected function lastUpdated()
    {
        if ($lastUpdated = $this->em->getRepository('FriggFlightBundle:LastUpdated')->findOneByClass(get_called_class())) {
            return $lastUpdated->getTimestamp();
        }

        return 0;
    }

    protected function setLastUpdated()
    {
        $importerClass = get_called_class();
        if (!$lastUpdated = $this->em->getRepository('FriggFlightBundle:LastUpdated')->findOneByClass($importerClass)) {
            $lastUpdated = new LastUpdated;
        }

        $lastUpdated->setClass($importerClass);
        $lastUpdated->setTimestamp(time());

        $this->em->persist($lastUpdated);
        $this->em->flush();
    }

    protected function getAvinorAirports()
    {
        return $this->em->createQueryBuilder()->select('a')
            ->from('FriggFlightBundle:Airport', 'a')
            ->where('a.is_avinor = :is_avinor')
            ->setParameter('is_avinor', true)
            ->getQuery()
            ->getResult();
    }
}
