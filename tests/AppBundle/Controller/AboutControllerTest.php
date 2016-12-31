<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Services\TestingManager;

class AboutControllerTest extends WebTestCase
{
    public function __construct()
    {
        $this->testingManager = new TestingManager();
    }
    
    public function testAboutPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'about'); 
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());    
    }
    
}
