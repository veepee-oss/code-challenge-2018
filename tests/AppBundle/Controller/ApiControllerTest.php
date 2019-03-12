<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    /**
     * Test API index doesn't exist
     */
    public function testIndexDoesntExist()
    {
        $client = static::createClient();
        $client->request('GET', '/api/');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test API get contest name returns static ontest
     */
    public function testGetApiContestNameReturnsStaticContest()
    {
        $client = static::createClient();
        $client->request('GET', '/api/contest/name');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $content = $client->getResponse()->getContent();

        $this->assertContains('David Amigo', $content);
        $this->assertContains('david.amigo@privalia.com', $content);
    }
}
