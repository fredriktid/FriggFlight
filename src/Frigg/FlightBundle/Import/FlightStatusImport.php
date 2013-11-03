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

    public function __toString()
    {
        return sprintf('Imported %d flight statuses (loaded %d)%s', count($this->flightStatuses), count($this->data), PHP_EOL);
    }

    public function run()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        // $source = $this->config['api_info', 'url']
        $source = sprintf('%s/../xml/flightStatuses.asp.xml', $this->container->get('kernel')->getRootDir());

        if ($data = $this->request($source)) {
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$flightStatus = $em->getRepository('FriggFlightBundle:FlightStatus')->findOneByCode($item['code'])) {
                        $flightStatus = new FlightStatus;
                    }

                    $flightStatus->setCode($item['code']);
                    $flightStatus->setTextEng($item['statusTextEn']);
                    $flightStatus->setTextNo($item['statusTextNo']);

                    $em->persist($flightStatus);
                    $em->flush();

                    $this->flightStatuses[] = $flightStatus;
                }
            }
        }
    }
}
