<?php

namespace Frigg\FlyBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class Flight
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $unique;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $flight;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $dom_int;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schedule_time;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $arr_dep;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $flight_status_time;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $check_in;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $gate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $delayed;

    /**
     * @ORM\ManyToOne(targetEntity="Airline", inversedBy="flights", cascade={"persist"})
     * @ORM\JoinColumn(name="airline_id", referencedColumnName="id", nullable=true)
     */
    private $airline;

    /**
     * @ORM\ManyToOne(targetEntity="Airport", inversedBy="flights", cascade={"persist"})
     * @ORM\JoinColumn(name="airport_id", referencedColumnName="id", nullable=true)
     */
    private $airport;

    /**
     * @ORM\ManyToOne(targetEntity="FlightStatus", inversedBy="flights", cascade={"persist"})
     * @ORM\JoinColumn(name="flight_status_id", referencedColumnName="id", nullable=true)
     */
    private $flight_status;

    /**
     * @ORM\ManyToMany(targetEntity="Airport", inversedBy="via_flights")
     * @ORM\JoinTable(
     *     name="AirportViaFlight",
     *     joinColumns={@ORM\JoinColumn(name="flight_id", referencedColumnName="id", nullable=true)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="airport_id", referencedColumnName="id", nullable=true)}
     * )
     */
    private $via_airports;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->via_airports = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set flight
     *
     * @param string $flight
     * @return Flight
     */
    public function setFlight($flight)
    {
        $this->flight = $flight;

        return $this;
    }

    /**
     * Get flight
     *
     * @return string
     */
    public function getFlight()
    {
        return $this->flight;
    }

    /**
     * Set unique
     *
     * @param integer $unique
     * @return Flight
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * Get unique
     *
     * @return integer
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * Set dom_int
     *
     * @param string $domInt
     * @return Flight
     */
    public function setDomInt($domInt)
    {
        $this->dom_int = $domInt;

        return $this;
    }

    /**
     * Get dom_int
     *
     * @return string
     */
    public function getDomInt()
    {
        return $this->dom_int;
    }

    /**
     * Set schedule_time
     *
     * @param string $scheduleTime
     * @return Flight
     */
    public function setScheduleTime($scheduleTime)
    {
        $this->schedule_time = $scheduleTime;

        return $this;
    }

    /**
     * Get schedule_time
     *
     * @return string
     */
    public function getScheduleTime()
    {
        return $this->schedule_time;
    }

    /**
     * Set arr_dep
     *
     * @param string $arrDep
     * @return Flight
     */
    public function setArrDep($arrDep)
    {
        $this->arr_dep = $arrDep;

        return $this;
    }

    /**
     * Get arr_dep
     *
     * @return string
     */
    public function getArrDep()
    {
        return $this->arr_dep;
    }

    /**
     * Set flight_status_time
     *
     * @param string $flightStatusTime
     * @return Flight
     */
    public function setFlightStatusTime($flightStatusTime)
    {
        $this->flight_status_time = $flightStatusTime;

        return $this;
    }

    /**
     * Get flight_status_time
     *
     * @return string
     */
    public function getFlightStatusTime()
    {
        return $this->flight_status_time;
    }

    /**
     * Set check_in
     *
     * @param string $checkIn
     * @return Flight
     */
    public function setCheckIn($checkIn)
    {
        $this->check_in = $checkIn;

        return $this;
    }

    /**
     * Get check_in
     *
     * @return string
     */
    public function getCheckIn()
    {
        return $this->check_in;
    }

    /**
     * Set gate
     *
     * @param string $gate
     * @return Flight
     */
    public function setGate($gate)
    {
        $this->gate = $gate;

        return $this;
    }

    /**
     * Get gate
     *
     * @return string
     */
    public function getGate()
    {
        return $this->gate;
    }

    /**
     * Set delayed
     *
     * @param boolean $delayed
     * @return Flight
     */
    public function setDelayed($delayed)
    {
        $this->delayed = $delayed;

        return $this;
    }

    /**
     * Get delayed
     *
     * @return boolean
     */
    public function getDelayed()
    {
        return $this->delayed;
    }

    /**
     * Set airline
     *
     * @param \Frigg\FlyBundle\Entity\Airline $airline
     * @return Flight
     */
    public function setAirline(\Frigg\FlyBundle\Entity\Airline $airline)
    {
        $this->airline = $airline;

        return $this;
    }

    /**
     * Get airline
     *
     * @return \Frigg\FlyBundle\Entity\Airline
     */
    public function getAirline()
    {
        return $this->airline;
    }

    /**
     * Set airport
     *
     * @param \Frigg\FlyBundle\Entity\Airport $airport
     * @return Flight
     */
    public function setAirport(\Frigg\FlyBundle\Entity\Airport $airport)
    {
        $this->airport = $airport;

        return $this;
    }

    /**
     * Get airport
     *
     * @return \Frigg\FlyBundle\Entity\Airport
     */
    public function getAirport()
    {
        return $this->airport;
    }

    /**
     * Set flight_status
     *
     * @param \Frigg\FlyBundle\Entity\FlightStatus $flightStatus
     * @return Flight
     */
    public function setFlightStatus(\Frigg\FlyBundle\Entity\FlightStatus $flightStatus)
    {
        $this->flight_status = $flightStatus;

        return $this;
    }

    /**
     * Get flight_status
     *
     * @return \Frigg\FlyBundle\Entity\FlightStatus
     */
    public function getFlightStatus()
    {
        return $this->flight_status;
    }

    /**
     * Add via_airports
     *
     * @param \Frigg\FlyBundle\Entity\Airport $viaAirports
     * @return Flight
     */
    public function addViaAirport(\Frigg\FlyBundle\Entity\Airport $viaAirports)
    {
        $this->via_airports[] = $viaAirports;

        return $this;
    }

    /**
     * Remove via_airports
     *
     * @param \Frigg\FlyBundle\Entity\Airport $viaAirports
     */
    public function removeViaAirport(\Frigg\FlyBundle\Entity\Airport $viaAirports)
    {
        $this->via_airports->removeElement($viaAirports);
    }

    /**
     * Get via_airports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViaAirports()
    {
        return $this->via_airports;
    }
}