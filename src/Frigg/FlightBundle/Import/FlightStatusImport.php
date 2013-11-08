<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\FlightStatus;

class FlightStatusImport extends ImportAbstract
{
    private $container;
    private $config;
    private $flightStatuses = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        $this->container = $container;
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function output()
    {
        return sprintf('Imported %d flight statuses', count($this->flightStatuses));
    }

    public function run()
    {
        if ($data = $this->request($this->config['source'])) {
            $em = $this->container->get('doctrine.orm.entity_manager');
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$flightStatus = $em->getRepository('FriggFlightBundle:FlightStatus')->findOneByCode($item['code'])) {
                        $flightStatus = new FlightStatus;
                    }

                    $flightStatus->setCode($item['code']);
                    $flightStatus->setTextEng($item['statusTextEn']);
                    $flightStatus->setTextNo($item['statusTextNo']);

                    $em->persist($flightStatus);
                    $this->flightStatuses[] = $flightStatus;
                }
            }
            $em->flush();
        }
    }
}
