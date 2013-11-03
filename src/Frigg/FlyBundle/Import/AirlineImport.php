<?php

namespace Frigg\FlyBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlyBundle\Entity\Airline;

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

    public function __toString()
    {
        return sprintf('Imported %d airlines (loaded %d)%s', count($this->airlines), count($this->data), PHP_EOL);
    }

    public function run()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        // $source = $this->config['api_info', 'url']
        $source = sprintf('%s/../xml/airlineNames.asp.xml', $this->container->get('kernel')->getRootDir());

        if ($data = $this->request($source)) {
            foreach ($data as $item) {
                if ($item['code'] && $item['code']) {
                    if (!$airline = $em->getRepository('FriggFlyBundle:Airline')->findOneByCode($item['code'])) {
                        $airline = new Airline;
                    }

                    $airline->setCode($item['code']);
                    $airline->setName($item['name']);

                    $em->persist($airline);
                    $em->flush();

                    $this->airlines[] = $airline;
                }
            }
        }
    }
}
