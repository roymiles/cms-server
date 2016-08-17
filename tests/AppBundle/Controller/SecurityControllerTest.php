<?php
// tests/AppBundle/Controller/SecurityControllerTest.php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    public function testValidLoginCredentialsWithUsername()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        
        // Set some values (valid credentials)
        $form['username'] = 'admin';
        $form['password'] = 'THEADMINPASSWORD';
        
        // Submit the form
        $crawler = $client->submit($form);
        
        // ... check redirect and cookies etc
    }
    
    public function testValidLoginCredentialsWithEmail(){}
    public function testLoginInValidCredentials(){}   
    public function testLogout(){}
    public function testSecuredAreaWithAuthorisation(){}
    public function testSecuredAreaWithoutAuthorisation(){}
    public function testRegisterAccount(){}
    public function isValidUserCookie(){}
    
}
