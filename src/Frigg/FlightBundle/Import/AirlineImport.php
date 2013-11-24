<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airline;

class AirlineImport extends AvinorImportAbstract
{
    protected $config = array();

    /**
     * Subclass constructor
     * @var ContainerInterface $container
     * @var array $configFile
     **/
    public function __construct(ContainerInterface $container, $configFile)
    {
        parent::__construct($container);
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    /**
     * Print import status
     * @return string
     **/
    public function output()
    {
        return sprintf('Imported %d airlines', count($this->data));
    }

    /**
     * Execute airline importer
     * @return AirlineImport
     **/
    public function run()
    {
        if ($response = $this->request($this->config['target'])) {
            foreach ($response as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$airline = $this->em->getRepository('FriggFlightBundle:Airline')->findOneByCode($item['code'])) {
                        $airline = new Airline;
                    }

                    $airline->setCode($item['code']);
                    $airline->setName($item['name']);

                    $this->em->persist($airline);
                    $this->data[] = $airline->getId();
                }
            }

            $this->em->flush();
            $this->setLastUpdated();
        }

        return $this;
    }
}
