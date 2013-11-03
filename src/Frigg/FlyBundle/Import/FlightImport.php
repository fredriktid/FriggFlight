<?php

namespace Frigg\FlyBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FlightImport extends ImportAbstract
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
        $source = sprintf('%s/../xml/flights.xml', $this->container->get('kernel')->getRootDir());
        foreach ($this->getData($source) as $domElement) {
            // import...
        }
    }
}
