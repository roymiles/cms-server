<?php

namespace Tests\AppBundle\Services;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestingManager //extends WebTestCase
{
    /*public function __construct(WebTestCase $wtc)
    {
        $this->wtc = $wtc;
    }*/
    
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
}
