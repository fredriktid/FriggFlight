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

    public function __toString()
    {
        return sprintf('Imported %d airports (loaded %d)%s', count($this->airports), count($this->data), PHP_EOL);
    }

    public function run()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        // $source = $this->config['api_info', 'url']
        $source = sprintf('%s/../xml/airportNames.asp.xml', $this->container->get('kernel')->getRootDir());

        if ($data = $this->request($source)) {
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$airport = $em->getRepository('FriggFlightBundle:Airport')->findOneByCode($item['code'])) {
                        $airport = new Airport;
                    }

                    $airport->setCode($item['code']);
                    $airport->setName($item['name']);

                    $em->persist($airport);
                    $em->flush();

                    $this->airports[] = $airport;
                }
            }
        }
    }
}
