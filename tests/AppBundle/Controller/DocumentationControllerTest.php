<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Services\TestingManager;

class DocumentationControllerTest extends WebTestCase
{
    public function __construct()
    {
        $this->testingManager = new TestingManager();
    }
    
    public function testDocumentationHomePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'documentation'); 
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());    
    }    
    
}
