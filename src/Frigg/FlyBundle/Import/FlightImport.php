<?php

namespace Frigg\FlyBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

class FlightImport extends ImportBase
{
    private $container;
    private $config;

    public function __construct(ContainerInterface $container, $configFile)
    {
        $this->container = $container;
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function run()
    {
        //var_dump($this->config);
        $rootDir = $this->container->get('kernel')->getRootDir();;
        // temporarily use local file
        if (($contents = file_get_contents($rootDir . '/../xml/flights.xml', 'r')) !== false) {
            $crawler = new Crawler($contents);
            foreach ($crawler as $domElement) {
                //var_dump($domElement->hasChildNodes()); die;
            }
        }
    }
}

