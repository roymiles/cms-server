<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require __DIR__.'/vendor/autoload.php';

// Does not work
$client = $this->get('guzzle.client');

$response = $client->post('/login');
echo $response;
echo "\n\n";