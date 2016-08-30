<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="LoginForm")
     */
    public function loginFormAction(Request $request)
    {
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        $_csrf_token = $AuthenticationManager::generate('csrf_token');
        return $this->render('default/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'login',
            '_csrf_token' => $_csrf_token
        ]);
    }
    
    /**
     * @Route("/processLogin", name="ProcessLoginRequest")
     */
    public function processLoginRequestAction(Request $request)
    {
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        $UsersManager = $this->get('app.UsersManager');
        $SitesManager = $this->get('app.SitesManager');
        $ErrorResponsesManager = $this->get('app.ErrorResponsesManager');
        
        // Check if the site token is local or external
        $isAjax = $ErrorResponsesManager->isAjax = $request->isXmlHttpRequest();
        
        // Is there a token in the URL
        $token = $request->attributes->get('token');
        if($token ===  null){
            return $ErrorResponsesManager->nullToken($request, $token);
        } 
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $token], ['limit' => 1]);
        if(!$Site){
            return $ErrorResponsesManager->invalidToken($request, $token);
        }
    
        // Check if the CSRF token is valid
        $_csrf_token = $request->attributes->get('_csrf_token');
        if(!$AuthenticationManager->check($_csrf_token)){
            $this->addFlash('login-error', 'Invalid CSRF token');
            return $ErrorResponsesManager->invalidCSRFToken($request, $_csrf_token);   
        }
        
        // Now perform error checks on user inputs
        $numErrors = 0;
        
        $UsernameOrEmail = $request->attributes->get('UsernameOrEmail');
        $Password = $request->attributes->get('Password');
        $User = $UsersManager->verifyCredentials($UsernameOrEmail, $Password, $Site->Id);
        if(!$User){
            $numErrors++; // Increment the error count
        }
        
        // Extra log in checks eg brute force
        
        if($numErrors == 0){
            // No errors
            
            // Set the appropriate session variables
            $this->get('session')->set('isLoggedIn', true);
            $this->get('session')->set('User', $User);
            
            return $this->redirectToRoute('Homepage');
        }else{
            if($isAjax){
                // Return json error
            }else{
                return $this->redirectToRoute('LoginForm');
            }
        }
    }    
}
