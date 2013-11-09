<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\FlightStatus;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Entity\Airport;

class FlightImport extends AvinorClient
{
    protected $config = array();
    protected $airportConfig = array();
    protected $flights = array();
    protected $onlyUpdates = false;
    protected $time = array(
        'from' => 1,
        'to' => 7
    );

    public function __construct(ContainerInterface $container, $configFile, $airportConfigFile)
    {
        parent::__construct($container);
        $this->config = Yaml::parse(file_get_contents($configFile));
        $this->airportConfig = Yaml::parse(file_get_contents($airportConfigFile));
    }

    public function output()
    {
        return sprintf('Imported %d flights', count($this->flights));
    }

    public function setUpdates($switch)
    {
        $this->onlyUpdates = (bool) $switch;
    }

    public function setTime($time)
    {
        if (is_array($time)) {
            $this->time = $time;
        }

        return $this;
    }

    public function run()
    {
        $lastUpdated = $this->lastUpdated();
        $avinorAirports = $this->airportConfig['avinor'];

        foreach ($this->getAvinorAirports() as $i => $airport) {
            $params = array();
            $params['airport'] = $airport->getCode();
            $params['TimeFrom'] = $this->time['from'];
            $params['TimeTo'] = $this->time['to'];

            if ($this->onlyUpdates) {
                $params['lastUpdate'] = sprintf('%sT%sZ', date('Y-m-d', $lastUpdated), date('H:i:s', $lastUpdated));
            }

            $target = sprintf('%s?%s', $this->config['target'], implode('&', array_map(function($key, $val) {
                return sprintf('%s=%s', urlencode($key), urlencode($val));
              },
              array_keys($params), $params))
            );

            if ($data = $this->request($target)) {
                foreach ($data->flights->flight as $flightNode) {
                    if (!$flightObject = $this->em->getRepository('FriggFlightBundle:Flight')->findOneByRemote($flightNode['uniqueID'])) {
                        $flightObject = new Flight;
                    }

                   if (!$airlineObject = $this->em->getRepository('FriggFlightBundle:Airline')->findOneByCode($flightNode->airline)) {
                        $airlineObject = new Airline;
                        $airlineObject->setCode($flightNode->airline);
                        $this->em->persist($airlineObject);
                        $this->em->flush();
                    }

                    if (!$airportObject = $this->em->getRepository('FriggFlightBundle:Airport')->findOneByCode($flightNode->airport)) {
                        $isAvinorAirport = (in_array($flightNode->airport, $avinorAirports));
                        $airportObject = new Airport;
                        $airportObject->setCode($flightNode->airport);
                        $airportObject->setIsAvinor($isAvinorAirport);
                        $this->em->persist($airportObject);
                        $this->em->flush();
                    }

                    if (isset($flightNode->status)) {
                        $flightObject->setFlightStatusTime($flightNode->status['time']);
                        if (!$flightStatusObject = $this->em->getRepository('FriggFlightBundle:FlightStatus')->findOneByCode($flightNode->status['code'])) {
                            $flightStatusObject = new FlightStatus;
                            $flightStatusObject->setCode($flightNode->status['code']);
                            $this->em->persist($flightStatusObject);
                            $this->em->flush();
                        }
                        $flightObject->setFlightStatus($flightStatusObject);
                    }

                    $isDelayed = (isset($flightNode->delayed) && $flightNode->delayed == 'Y');
                    $gate = (isset($flightNode->gate) ? $flightNode->gate : '');

                    $flightObject->setRemote($flightNode['uniqueID']);
                    $flightObject->setIdentifier($flightNode->flight_id);
                    $flightObject->setDomInt($flightNode->dom_int);
                    $flightObject->setScheduleTime($flightNode->schedule_time);
                    $flightObject->setArrDep($flightNode->arr_dep);
                    $flightObject->setCheckIn($flightNode->check_in);
                    $flightObject->setAirline($airlineObject);
                    $flightObject->setAirport($airportObject);
                    $flightObject->setIsDelayed($isDelayed);
                    $flightObject->setgate($gate);

                    $this->em->persist($flightObject);
                    $this->flights[] = $flightObject->getId();
                }

                $this->em->flush();
                $this->setLastUpdated();

                // tmp
                break;
            }
        }
    }
}
