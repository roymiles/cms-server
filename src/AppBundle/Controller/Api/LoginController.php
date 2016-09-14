<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LoginController extends Controller
{
    /**
     * @Route("/api/login", name="ApiLoginForm")
     */
    public function loginFormAction(Request $request)
    {
        // Render the form
    }
    
    /**
     * @Route("/api/processLogin", name="ApiProcessLoginRequest")
     * @Method({"POST"})
     */
    public function processLoginRequestAction(Request $request)
    {
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        $UsersManager = $this->get('app.UsersManager');
        $SitesManager = $this->get('app.SitesManager');
        $LoggerManager = $this->get('app.LoggerManager');
        
        // Is there a token in the URL
        $site_token = $request->request->get('site_token');
        
        if($site_token ===  null){
            // No Token supplied
            $LoggerManager->error('No site token supplied', ['site_token' => $site_token]);
            $this->addFlash('loginErrors', "No token given");
            
            if($isAjax){
                // Return json error
                return $this->jsonError("No token given");
            }else{
                return $this->redirectToRoute('LoginForm');
            }   
        }         
    }
}
