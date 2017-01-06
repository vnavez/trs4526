<?php

namespace FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestControllerControllerTest extends WebTestCase
{
    public function testSend()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/send');
    }

}
