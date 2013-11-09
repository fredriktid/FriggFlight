<?php

namespace Frigg\FlightBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 */
class FlightStatus
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=1, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text_eng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text_no;

    /**
     * @ORM\OneToMany(targetEntity="Flight", mappedBy="flight_status")
     */
    private $flights;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->flights = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return FlightStatus
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
     * Set text_eng
     *
     * @param string $textEng
     * @return FlightStatus
     */
    public function setTextEng($textEng)
    {
        $this->text_eng = $textEng;

        return $this;
    }

    /**
     * Get text_eng
     *
     * @return string
     */
    public function getTextEng()
    {
        return $this->text_eng;
    }

    /**
     * Set text_no
     *
     * @param string $textNo
     * @return FlightStatus
     */
    public function setTextNo($textNo)
    {
        $this->text_no = $textNo;

        return $this;
    }

    /**
     * Get text_no
     *
     * @return string
     */
    public function getTextNo()
    {
        return $this->text_no;
    }

    /**
     * Add flights
     *
     * @param \Frigg\FlightBundle\Entity\Flight $flights
     * @return FlightStatus
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
}