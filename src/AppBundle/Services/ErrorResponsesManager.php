<?php

namespace AppBundle\Services;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ErrorResponsesManager extends Controller
{   
    public $isAjax;
    public $flashName = false;
    public $errorRedirect = false;
    
    private $logger;
    public function __construct(LoggerManager $logger)
    {
        $this->logger = $logger;
    }
    
    // All json errors should have the same structure
    private function jsonError($error){
        return new JsonResponse([
            'response' => 'error',
            'error' => $error
        ]);
    }
    
    // Render a generic error message
    private function genericError($errorMessage){
        if($this->isAjax){
            // Return json error
            return $this->jsonError($errorMessage);
        }else{
            /*
             * Store error message in a flash bag
             * If no flash bag supplied, store in generic 'error' flash bag
             */
            $f = ($flashName ? $flashName : 'error');
            $this->addFlash($f, $errorMessage);
            
            if($this->errorRedirect){
                return $this->redirectToRoute($this->errorRedirect['name'], $this->errorRedirect['parameters']);
            }else{
                return $this->render('default/error-response.html.twig', [
                    'ErrorResponse' => $errorMessage
                ]);
            }
        }   
    }
    
    public function nullToken($request, $token){
        $this->logger($request, $token)
        return $this->genericError("No token given");
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
