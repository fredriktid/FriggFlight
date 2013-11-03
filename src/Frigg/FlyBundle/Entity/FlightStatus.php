<?php

namespace Frigg\FlyBundle\Entity;

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
}