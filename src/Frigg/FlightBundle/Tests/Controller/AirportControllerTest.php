<?php

namespace Frigg\FlightBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AirportControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello/Fredrik');

        $this->assertTrue($crawler->filter('html:contains("Hello Fredrik")')->count() > 0);
    }
}
