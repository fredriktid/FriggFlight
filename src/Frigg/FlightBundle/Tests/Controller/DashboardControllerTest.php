<?php

namespace Frigg\FlightBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello/Fredrik');

        $this->assertTrue($crawler->filter('html:contains("Hello Fredrik")')->count() > 0);
    }
}
