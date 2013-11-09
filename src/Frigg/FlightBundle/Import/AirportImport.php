<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airport;

class AirportImport extends AvinorClient
{
    protected $config = array();
    protected $airports = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        parent::__construct($container);
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function output()
    {
        return sprintf('Imported %d airports', count($this->airports));
    }

    public function run()
    {
        if ($data = $this->request($this->config['target'])) {
            $avinorAirports = $this->config['avinor'];
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    $isAvinorAirport = (in_array($item['code'], $avinorAirports));
                    if (!$airport = $this->em->getRepository('FriggFlightBundle:Airport')->findOneByCode($item['code'])) {
                        $airport = new Airport;
                    }

                    $airport->setCode($item['code']);
                    $airport->setName($item['name']);
                    $airport->setIsAvinor($isAvinorAirport);

                    $this->em->persist($airport);
                    $this->airports[] = $airport->getId();
                }
            }

            $this->em->flush();
            $this->setLastUpdated();
        }
    }
}
