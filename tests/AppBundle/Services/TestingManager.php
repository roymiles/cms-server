<?php

namespace Tests\AppBundle\Services;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestingManager //extends WebTestCase
{
    const URL = "http://localhost/";
    
    // The <title> usually contains information about the error
    function getError($response, $crawler) {
        if (!$response->isSuccessful()) {
            $block = $crawler->filter('title');
            if ($block->count()) {
                $error = $block->text();
                return $error;
            }
            
            return null;
        }
    }
    
    
    function login(){
        // 'browse' to the page
        $client = static::createClient();
        $crawler = $client->request('GET', TestingManager::URL . 'login');
        
        $form = $crawler->selectButton('Login')->form();
        $form['Username'] = 'admin';
        $form['Password'] = 'admin';

        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect(); // Will redirect back to login form
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Logout")')->count());
        
        return $crawler;
    }
}
