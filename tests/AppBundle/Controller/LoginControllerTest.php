<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Services\TestingManager;

class LoginControllerTest extends WebTestCase
{
    public function __construct()
    {
        $this->testingManager = new TestingManager();
    }
    
    // Check the login page is working
    public function testLoginPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'login'); 
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());    
    }
    
    
    // Check that the login form returns an error if the credentials are incorrect
    public function testInvalidLogin()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'login');
        $error = $this->testingManager->getError($client->getResponse(), $crawler);
        
        $form = $crawler->selectButton('Login')->form();
        // Invalid credentials
        $form['Username'] = '-';
        $form['Password'] = '-';        
        
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
           
        $this->assertContains(
            'Username could not be found.',
            $client->getResponse()->getContent()
        );
    }
    
    // Check the login form works when valid credentials are supplied
    public function testValidLogin()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'login');
        
        $form = $crawler->selectButton('Login')->form();
        // Correct credentials
        $form['Username'] = 'admin';
        $form['Password'] = 'admin';

        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Logout")')->count());
    }
}
