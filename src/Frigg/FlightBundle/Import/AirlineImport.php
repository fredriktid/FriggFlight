<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Airline;

class AirlineImport extends AvinorImportAbstract
{
    protected $config = array();
    protected $airlines = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        parent::__construct($container);
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function output()
    {
        return sprintf('Imported %d airlines', count($this->airlines));
    }

    public function run()
    {
        if ($data = $this->request($this->config['target'])) {
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$airline = $this->em->getRepository('FriggFlightBundle:Airline')->findOneByCode($item['code'])) {
                        $airline = new Airline;
                    }

                    $airline->setCode($item['code']);
                    $airline->setName($item['name']);

                    $this->em->persist($airline);
                    $this->airlines[] = $airline->getId();
                }
            }

            $this->em->flush();
            $this->setLastUpdated();
        }
    }
}
