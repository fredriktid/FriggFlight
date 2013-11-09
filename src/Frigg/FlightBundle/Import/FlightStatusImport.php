<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\FlightStatus;

class FlightStatusImport extends AvinorClient
{
    protected $config = array();
    protected $flightStatuses = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        parent::__construct($container);
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function output()
    {
        return sprintf('Imported %d flight statuses', count($this->flightStatuses));
    }

    public function run()
    {
        if ($data = $this->request($this->config['target'])) {
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$flightStatus = $this->em->getRepository('FriggFlightBundle:FlightStatus')->findOneByCode($item['code'])) {
                        $flightStatus = new FlightStatus;
                    }

                    $flightStatus->setCode($item['code']);
                    $flightStatus->setTextEng($item['statusTextEn']);
                    $flightStatus->setTextNo($item['statusTextNo']);

                    $this->em->persist($flightStatus);
                    $this->flightStatuses[] = $flightStatus->getId();
                }
            }

            $this->em->flush();
            $this->setLastUpdated();
        }
    }
}
