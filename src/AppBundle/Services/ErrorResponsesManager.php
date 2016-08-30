<?php

namespace AppBundle\Services;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ErrorResponsesManager
{   
    // All json errors should have the same structure
    private function jsonError($error){
        return new JsonResponse([
            'response' => 'error',
            'error' => $error
        ]);
    } 
}
