<?php

namespace Frigg\FlightBundle\Import;

use Frigg\FlightBundle\Entity\LastUpdated;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AvinorImportAbstract
{
    protected $em = null;
    protected $container = null;

    protected $response = array();
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
                $this->response = simplexml_load_string($content);
                return $this->response;
            } catch(\Exception $e) {
                printf('Error: %s%s', $e->getMessage(), PHP_EOL);
            }
        }

        return false;
    }

    protected function getLastUpdated()
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

}
