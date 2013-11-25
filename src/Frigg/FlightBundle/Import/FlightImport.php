<?php

namespace Frigg\FlightBundle\Import;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\FlightStatus;
use Frigg\FlightBundle\Entity\Airline;
use Frigg\FlightBundle\Entity\Airport;

class FlightImport extends AvinorImportAbstract
{
    protected $config;
    protected $time;
    protected $onlyUpdates = false;

    /**
     * Subclass constructor
     * @var ContainerInterface $container
     * @var array $configFile
     **/
    public function __construct(ContainerInterface $container, $configFile)
    {
        parent::__construct($container);
        $this->config = Yaml::parse(file_get_contents($configFile));
        $this->time = array(
            'from' => $this->config['import']['time']['default']['from'],
            'to' => $this->config['import']['time']['default']['to']
        );
    }

    /**
     * Print import status
     * @return string
     **/
    public function output()
    {
        return sprintf('Imported %d flights', count($this->data));
    }

    /**
     * Switch for only-updates vs full import
     * @return FlightImport
     **/
    public function setUpdates($switch)
    {
        $this->onlyUpdates = (bool) $switch;
        return $this;
    }

    /**
     * Set a new time interval for import
     * @var array $time Interval of hours for import
     * @return FlightImport
     **/
    public function setTime($time)
    {
        if (is_array($time)) {
            $this->time = $time;
        }

        return $this;
    }

    /**
     * Execute flight importer
     * @return FlightImport
     **/
    public function run()
    {
        $lastUpdated = $this->getLastUpdated();
        $airportService = $this->container->get('frigg_flight.airport_service');

        foreach ($airportService->getAvinorAirports() as $i => $airport) {

            // temporarily just oslo
            if (!in_array($airport->getCode(), array('OSL'))) {
                continue;
            }

            $params = array();
            $params['airport'] = $airport->getCode();
            $params['TimeFrom'] = $this->time['from'];
            $params['TimeTo'] = $this->time['to'];

            if ($this->onlyUpdates) {
                $params['lastUpdate'] = sprintf('%sT%sZ', date('Y-m-d', $lastUpdated), date('H:i:s', $lastUpdated));
            }

            // build endpoint address
            $target = sprintf('%s?%s', $this->config['import']['endpoint'], implode('&', array_map(function($key, $val) {
                return sprintf('%s=%s', urlencode($key), urlencode($val));
              },
              array_keys($params), $params))
            );

            // do request
            if ($response = $this->request($target)) {
                // handle response
                foreach ($response->flights->flight as $flightNode) {
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
                        $flightObject->setFlightStatusTime(new \DateTime(date('Y-m-d H:i:s', strtotime($flightNode->status['time']))));
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
                    $flightObject->setCode($flightNode->flight_id);
                    $flightObject->setDomInt($flightNode->dom_int);
                    $flightObject->setScheduleTime(new \DateTime(date('Y-m-d H:i:s', strtotime($flightNode->schedule_time))));
                    $flightObject->setArrDep($flightNode->arr_dep);
                    $flightObject->setCheckIn($flightNode->check_in);
                    $flightObject->setAirline($airlineObject);
                    $flightObject->setAirport($airport);
                    $flightObject->setOtherAirport($airportObject);

                    $flightObject->setIsDelayed($isDelayed);
                    $flightObject->setgate($gate);

                    $this->em->persist($flightObject);
                    $this->data[] = $flightObject->getId();
                }

                $this->em->flush();
                $this->setLastUpdated();
            }
        }

        return $this;
    }
}
