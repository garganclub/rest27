<?php

namespace Animaux\AnimauxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    // phpunit -c app src/Animaux/AnimauxBundle/Tests/Controller/DefaultControllerTest.php
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');        
        $this->assertTrue($crawler->filter('html:contains("Aigle")')->count() > 0);
    }
}