<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airport;

class AirportImport extends ImportAbstract
{
    private $container;
    private $config;
    private $airports = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        $this->container = $container;
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function output()
    {
        return sprintf('Imported %d airports', count($this->airports));
    }

    public function run()
    {
        if ($data = $this->request($this->config['source'])) {
            $em = $this->container->get('doctrine.orm.entity_manager');
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$airport = $em->getRepository('FriggFlightBundle:Airport')->findOneByCode($item['code'])) {
                        $airport = new Airport;
                    }

                    $airport->setCode($item['code']);
                    $airport->setName($item['name']);

                    $em->persist($airport);
                    $this->airports[] = $airport;
                }
            }
            $em->flush();
        }
    }
}
