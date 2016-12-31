<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Services\TestingManager;

class HomepageControllerTest extends WebTestCase
{
    public function __construct()
    {
        $this->testingManager = new TestingManager();
    }
    
    public function testHomePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL); 
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());    
    }
    
}
