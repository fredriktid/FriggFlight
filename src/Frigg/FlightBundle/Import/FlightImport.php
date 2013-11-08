<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\FlightStatus;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Entity\Airport;

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

    public function output()
    {
        return sprintf('Imported %d flights', count($this->flights));
    }

    public function run()
    {
        if (!isset($this->config['airports'])) {
            return false;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');

        foreach($this->config['airports'] as $airportImportCode) {

            $params = array(
                'airport' => $airportImportCode,
                'TimeFrom' => 1,
                'TimeTo' => 182,
                'Direction' => 'D',
                'lastUpdate' => '2009-03-10T15:03:00Z'
            );

            $source = sprintf('%s?%s', $this->config['source'], implode('&', array_map(function($key, $val) {
                return sprintf('%s=%s', urlencode($key), urlencode($val));
              },
              array_keys($params), $params))
            );

            if ($data = $this->request($source)) {
                foreach ($data->flights->flight as $flightNode) {
                    if (!$flightObject = $em->getRepository('FriggFlightBundle:Flight')->findOneByRemote($flightNode['uniqueID'])) {
                        $flightObject = new Flight;
                    }

                    $flightObject->setRemote($flightNode['uniqueID']);
                    $flightObject->setIdentifier($flightNode->flight_id);

                    $flightObject->setDomInt($flightNode->dom_int);
                    $flightObject->setScheduleTime($flightNode->schedule_time);
                    $flightObject->setArrDep($flightNode->arr_dep);
                    $flightObject->setCheckIn($flightNode->check_in);

                    if (!$airlineObject = $em->getRepository('FriggFlightBundle:Airline')->findOneByCode($flightNode->airline)) {
                        $airlineObject = new Airline;
                        $airlineObject->setCode($flightNode->airline);
                        $em->persist($airlineObject);
                        $em->flush();
                    }

                    $flightObject->setAirline($airlineObject);

                    if (!$airportObject = $em->getRepository('FriggFlightBundle:Airport')->findOneByCode($flightNode->airport)) {
                        $airportObject = new Airport;
                        $airportObject->setCode($flightNode->airport);
                        $em->persist($airportObject);
                        $em->flush();
                    }

                    $flightObject->setAirport($airportObject);

                    if (isset($flightNode->status)) {
                        $flightObject->setFlightStatusTime($flightNode->status['time']);
                        if (!$flightStatusObject = $em->getRepository('FriggFlightBundle:FlightStatus')->findOneByCode($flightNode->status['code'])) {
                            $flightStatusObject = new FlightStatus;
                            $flightStatusObject->setCode($flightNode->status['code']);
                            $em->persist($flightStatusObject);
                            $em->flush();
                        }
                        $flightObject->setFlightStatus($flightStatusObject);
                    }

                    $isDelayed = (isset($flightNode->delayed) && $flightNode->delayed == 'Y') ? true : false;
                    $flightObject->setIsDelayed($isDelayed);

                    $gate = (isset($flightNode->gate)) ? $flightNode->gate : '';
                    $flightObject->setgate($gate);

                    $em->persist($flightObject);
                    $this->flights[] = $flightNode;
                }
                $em->flush();
            }
        }
    }
}
