<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Flight;

class FlightImport extends ImportAbstract
{
    private $container;
    private $config;
    private $flights = array();

    public function __construct(ContainerInterface $container, $configFile)
    {
        $this->container = $container;
        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    public function __toString()
    {
        return sprintf('Imported %d flights (loaded %d)%s', count($this->flights), count($this->data), PHP_EOL);
    }

    public function run()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $source = sprintf('%s/../xml/flights.xml', $this->container->get('kernel')->getRootDir());
        if ($data = $this->request($source)) {
            if (is_object($data)) {
                foreach ($data->flights as $item) {
                    foreach ($item->flight as $flight)
                    {
                        if (!$flightObject = $em->getRepository('FriggFlightBundle:Flight')->findOneByUnique($flight['uniqueID'])) {
                            $flightObject = new Flight;
                        }

                        $flightObject->setUnique((int) $flight['uniqueID']);
                        $flightObject->setFlight((string) $flight->flight_id);
                        $flightObject->setDomInt((string) $flight->dom_int);
                        $flightObject->setScheduleTime((string) $flight->schedule_time);
                        $flightObject->setArrDep((string) $flight->arr_dep);
                        $flightObject->setCheckIn((string) $flight->check_in);

                        if ($airlineObject = $em->getRepository('FriggFlightBundle:Airline')->findOneByCode($flight->airline)) {
                            $flightObject->setAirline($airlineObject);
                        };

                        if ($airportObject = $em->getRepository('FriggFlightBundle:Airport')->findOneByCode($flight->airport)) {
                            $flightObject->setAirport($airportObject);
                        };

                        if (isset($flight->status)) {
                            if ($flightStatus = $em->getRepository('FriggFlightBundle:FlightStatus')->findOneByCode($flight->status)) {
                                $flightObject->setFlightStatus($flightStatus);
                            };
                        }

                        $delayed = (isset($item->delayed) && $item->delayed == 'Y') ? true : false;
                        $flightObject->setDelayed($delayed);

                        $gate = (isset($item->gate)) ? $item->gate : '';
                        $flightObject->setgate($gate);

                        $em->persist($flightObject);
                        $em->flush();
                    }
                }
            }
        }
    }
}
