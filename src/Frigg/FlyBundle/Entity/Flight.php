<?php
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
     * @ORM\Column(type="integer", unique=true, nullable=true)
     */
    private $flight_id;

    /**
     * @ORM\Column(type="string", unique=true, length=1, nullable=true)
     */
    private $dom_int;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $schedule_time;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Airline", inversedBy="flights")
     * @ORM\JoinColumn(name="airline_id", referencedColumnName="id", nullable=false)
     */
    private $airline;

    /**
     * @ORM\ManyToOne(targetEntity="Airport", inversedBy="flights")
     * @ORM\JoinColumn(name="airport_id", referencedColumnName="id", nullable=false)
     */
    private $airport;

    /**
     * @ORM\ManyToOne(targetEntity="FlightStatus", inversedBy="flights")
     * @ORM\JoinColumn(name="flight_status_id", referencedColumnName="id", nullable=false)
     */
    private $flight_status;

    /**
     * @ORM\ManyToMany(targetEntity="Airport", inversedBy="via_flights")
     * @ORM\JoinTable(
     *     name="AirportViaFlight",
     *     joinColumns={@ORM\JoinColumn(name="flight_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="airport_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $via_airports;
}