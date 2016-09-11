<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Services\TestingManager;

class LoginControllerTest extends WebTestCase
{
    public function __construct()
    {
        $this->tm = new TestingManager();
    }
    
    public function testLoginPage()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', 'http://localhost/login'); 
        $response = $client->getResponse();
        $error = $this->tm->getError($response, $crawler);
        echo "testLoginPage() response <title> = ";
        echo $error ? $error : '';
        echo "\n";
        
        // 200 => 'OK'
        $this->assertEquals(200, $response->getStatusCode());    
    }
    
    
    public function testValidLogin()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://localhost/login');
        $response = $client->getResponse();
        $error = $this->tm->getError($response, $crawler);
        echo "testValidLogin() response <title> = ";
        echo $error ? $error : '';
        echo "\n";
        
        // The name of our button is "Login"
        $form = $crawler->selectButton('Login')->form();
        
        // Empty details
        $form['Username'] = '';
        $form['Password'] = '';
              
        // Submit the form  
        $client->submit($form);
        
        // Follow redirect
        $client->followRedirect();
        
        // Should redirect back to login form
        $this->assertContains('login', $client->getResponse()->headers->get('location'));
        $this->assertContains(
            'Email or Username does not correspond to a user',
            $client->getResponse()->getContent()
        );
    }
    
    /*public function testInvalidLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        
        // The name of our button is "Login"
        $form = $crawler->selectButton('Login')->form();
        
        // Empty details
        $form['Username'] = '';
        $form['Password'] = '';
              
        // Submit the form  
        $crawler = $client->submit($form);
        
        // 200 => 'OK'
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Should redirect back to login form
        $this->assertRegExp('/\//', $client->getResponse()->headers->get('location'));
        $this->assertRegexp(
            'Email or Username does not correspond to a user',
            $client->getResponse()->getContent()
        );
    }*/  
}
