<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airline;

class AirlineImport extends ImportAbstract
{
    private $container;
    private $config;
    private $airlines = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        $this->container = $container;
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function output()
    {
        return sprintf('Imported %d airlines', count($this->airlines));
    }

    public function run()
    {
        if ($data = $this->request($this->config['source'])) {
            $em = $this->container->get('doctrine.orm.entity_manager');
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$airline = $em->getRepository('FriggFlightBundle:Airline')->findOneByCode($item['code'])) {
                        $airline = new Airline;
                    }

                    $airline->setCode($item['code']);
                    $airline->setName($item['name']);

                    $em->persist($airline);

                    $this->airlines[] = $airline;
                }
            }
            $em->flush();
        }
    }
}
