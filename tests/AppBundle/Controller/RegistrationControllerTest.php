<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Services\TestingManager;

class RegistrationControllerTest extends WebTestCase
{
    public function __construct()
    {
        $this->testingManager = new TestingManager();
    }
    
    public function testRegisterPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'register'); 
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());    
    }
    
    
    public function testInvalidRegistration()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'register');
        $error = $this->testingManager->getError($client->getResponse(), $crawler);
        
        $form = $crawler->selectButton('Sign Up')->form();
        
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect(); // Will redirect back to login form
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // Expected Username errors
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Username must contain at least 4 characters")')->count());
        
        // Expected Email errors
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email must contain at least 4 characters")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Invalid email address")')->count());
        
        // Expected Password errors
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password must contain at least 8 characters")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password must contain at least 1 number")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password must contain at least 1 capital letter")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password must contain at least 1 lowercase letter")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password should not be the same as the username")')->count());
        
    }
    
    public function testValidRegistration()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'register');
        
        $form = $crawler->selectButton('Sign Up')->form();
        $password = 'unittest';
        $identifier = uniqid('unittest_');
        
        $form['user[Username]'] = $identifier;
        $form['user[Email]'] = $identifier . '@example.com';
        $form['user[Password]'] = $password;
        $form['repeatPassword'] = $password;

        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect(); // Will redirect back to login form
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Logout")')->count());
    }
}
