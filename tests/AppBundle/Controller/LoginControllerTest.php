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
        //echo "testLoginPage() response <title> = ";
        //echo $error ? $error : '';
        //echo "\n";
        
        // 200 => 'OK'
        $this->assertTrue($client->getResponse()->isSuccessful());    
    }
    
    
    public function testInvalidLogin()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://localhost/login');
        $error = $this->tm->getError($client->getResponse(), $crawler);
        //echo "testValidLogin() response <title> = ";
        //echo $error ? $error : '';
        //echo "\n";
        
        $form = $crawler->selectButton('Login')->form();
        
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect(); // Will redirect back to login form
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertRegexp(
            '/Username could not be found./',
            $client->getResponse()->getContent()
        );
    }
    
    public function testValidLogin()
    {
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://localhost/login');
        $error = $this->tm->getError($client->getResponse(), $crawler);
        //echo "testValidLogin() response <title> = ";
        //echo $error ? $error : '';
        //echo "\n";
        
        $form = $crawler->selectButton('Login')->form();
        $form['Username'] = 'admin';
        $form['Password'] = 'admin';

        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect(); // Will redirect back to login form
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Logout")')->count());
    }
}
