<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginPage()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/login'); 
        //var_dump($client->getRequest());
        // 200 => 'OK'
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    
    public function testValidLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'http://localhost/login');
        //dump($client->getContent());
        // The name of our button is "Login"
        $form = $crawler->selectButton('Login')->form();
        
        // Empty details
        $form['Username'] = '';
        $form['Password'] = '';
              
        // Submit the form  
        $crawler = $client->submit($form);
        
        // Should redirect back to login form
       //var_dump($client->getResponse()->headers->get('location')); die();
        $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));
        $this->assertRegexp(
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
