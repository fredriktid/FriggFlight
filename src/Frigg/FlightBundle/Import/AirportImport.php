<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airport;

class AirportImport extends AvinorImportAbstract
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
     * Print output of import status
     * @return string
     **/
    public function output()
    {
        return sprintf('Imported %d airports', count($this->data));
    }

    /**
     * Execute airport importer
     * @return AirportImport
     **/
    public function run()
    {
        if ($response = $this->request($this->config['target'])) {
            $avinorAirports = (isset($this->config['avinor'])) ? $this->config['avinor'] : array();
            foreach ($response as $item) {
                if ($item['code'] && $item['code']) {
                    $isAvinorAirport = (in_array($item['code'], $avinorAirports));
                    if (!$airport = $this->em->getRepository('FriggFlightBundle:Airport')->findOneByCode($item['code'])) {
                        $airport = new Airport;
                    }

                    $airport->setCode($item['code']);
                    $airport->setName($item['name']);
                    $airport->setIsAvinor($isAvinorAirport);

                    $this->em->persist($airport);
                    $this->data[] = $airport->getId();
                }
            }

            $this->em->flush();
            $this->setLastUpdated();
        }

        return $this;
    }
}
