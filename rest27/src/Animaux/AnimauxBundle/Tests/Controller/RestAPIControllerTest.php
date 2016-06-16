<?php

namespace Animaux\AnimauxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestAPIControllerTest extends WebTestCase
{
    // phpunit -c app src/Animaux/AnimauxBundle/Tests/Controller/RestAPIControllerTest.php
    public function testCget()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animaux');
        $response = $client->getResponse();
        $resultat = json_decode($response->getContent(), true);
        $tableau = array();
        for($i=0; $i<count($resultat); $i++) {
        	array_push($tableau, $resultat[$i]);
        }
        $this->assertEquals(200, $response->getStatusCode());
        return $tableau;
    }
    public function testGet()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animaux/2');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
			'{"id":2,"classe":"Oiseaux","ordre":"Carnivores","famille":"Rapaces","nom":"Aigle"}',
			$client->getResponse()->getContent()
		);
    }
    /**
     * @dataProvider testCget
     */
    public function testGetTestCget($i)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animaux/'. $i);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    public function testCgetFiltre()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animaux?classe=Mammifères');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $resultat = json_decode($response->getContent(), true);
        $tableau = array();
        for($i=0; $i<count($resultat); $i++) {
        	array_push($tableau, $resultat[$i]);
        }
        return $tableau;
    }
    /**
     * @dataProvider testCgetFiltre
     */
    public function testGetTestCgetFiltre($i, $c)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animaux?classe=Mammifères');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Mammifères', $c);
    }
    public function fournisseur()
    {
        return array(
          array(100),
          array('n\'importequoi')
          //array(1)
        );
    }
    /**
     * @dataProvider fournisseur
     */
	public function testGetException($id)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animaux/'. $id);
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode(), 'Pas de message d\'erreur car l\'id n°'. $id .' est valide.');
        $resultat = json_decode($response->getContent(), true);
        if(preg_match('/^\d+$/', $id)) {
        	$this->assertEquals('L\'id n°'. $id .' n\'existe pas.', $resultat['error']['exception'][0]['message']);
        }
        else {
        	$this->assertEquals('L\'identifiant demandé : '. $id .' doit être un numéro.', $resultat['error']['exception'][0]['message']);
        }
    }
}