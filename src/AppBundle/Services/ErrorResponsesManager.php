<?php

namespace AppBundle\Services;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ErrorResponsesManager extends Controller
{   
    public function nullToken($request, $token){
        return $this->render('default/error-response.html.twig', [
            'ErrorResponse' => "No token given"
        ]);
    }
    
    public function invalidToken($request, $token){
        return $this->render('default/error-response.html.twig', [
            'ErrorResponse' => "Invalid token"
        ]);
    }  
    
    public function nullParameter($request, $parameter){
        return $this->render('default/error-response.html.twig', [
            'ErrorResponse' => $parameter . " not given"
        ]);
    }   
    
    public function noUserFound($request, $parameter){
        return $this->render('default/error-response.html.twig', [
            'ErrorResponse' => "No user found"
        ]);
    } 
}
