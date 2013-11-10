<?php

namespace Frigg\FlightBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Frigg\FlightBundle\Entity\Airport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Frigg\FlightBundle\Entity\AirportRepository")
 *
 * @ExclusionPolicy("all")
 */
class Airport
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=20, nullable=false)
     *
     * @Expose
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Expose
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Expose
     */
    private $is_avinor;

    /**
     * @ORM\OneToMany(targetEntity="Flight", mappedBy="airport")
     */
    private $flights;

    /**
     * @ORM\ManyToMany(targetEntity="Flight", mappedBy="via_airports")
     */
    private $via_flights;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->flights = new \Doctrine\Common\Collections\ArrayCollection();
        $this->via_flights = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set code
     *
     * @param string $code
     * @return Airport
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Airport
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add flights
     *
     * @param \Frigg\FlightBundle\Entity\Flight $flights
     * @return Airport
     */
    public function addFlight(\Frigg\FlightBundle\Entity\Flight $flights)
    {
        $this->flights[] = $flights;

        return $this;
    }

    /**
     * Remove flights
     *
     * @param \Frigg\FlightBundle\Entity\Flight $flights
     */
    public function removeFlight(\Frigg\FlightBundle\Entity\Flight $flights)
    {
        $this->flights->removeElement($flights);
    }

    /**
     * Get flights
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlights()
    {
        return $this->flights;
    }

    /**
     * Add via_flights
     *
     * @param \Frigg\FlightBundle\Entity\Flight $viaFlights
     * @return Airport
     */
    public function addViaFlight(\Frigg\FlightBundle\Entity\Flight $viaFlights)
    {
        $this->via_flights[] = $viaFlights;

        return $this;
    }

    /**
     * Remove via_flights
     *
     * @param \Frigg\FlightBundle\Entity\Flight $viaFlights
     */
    public function removeViaFlight(\Frigg\FlightBundle\Entity\Flight $viaFlights)
    {
        $this->via_flights->removeElement($viaFlights);
    }

    /**
     * Get via_flights
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViaFlights()
    {
        return $this->via_flights;
    }

    /**
     * Set is_avinor
     *
     * @param boolean $isAvinor
     * @return Airport
     */
    public function setIsAvinor($isAvinor)
    {
        $this->is_avinor = $isAvinor;

        return $this;
    }

    /**
     * Get is_avinor
     *
     * @return boolean
     */
    public function getIsAvinor()
    {
        return $this->is_avinor;
    }
}