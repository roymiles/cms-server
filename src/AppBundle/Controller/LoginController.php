<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="LoginForm")
     */
    public function loginFormAction(Request $request)
    {
        /*
         *  Symfony automatically starts sessions for you
         *  http://stackoverflow.com/questions/21276048/failed-to-start-the-session-already-started-by-php-session-is-set-500-inte
         */
        $session = $request->getSession();
        
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        
        
        $csrf_token = $AuthenticationManager->csrf_generate('csrf_token');
        return $this->render('default/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'login',
            'csrf_token' => $csrf_token,
            
            'session_data' => $session->all()
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
        $LoggerManager = $this->get('app.LoggerManager');
        
        // Check if the site token is local or external
        $isAjax = $request->isXmlHttpRequest();
        
        // Is there a token in the URL
        $site_token = $request->request->get('site_token');
        
        if($site_token ===  null){
            // No Token supplied
            $LoggerManager->error($request, $site_token);
            $this->addFlash('login-error', "No token given");
            
            if($isAjax){
                // Return json error
                return $this->jsonError("No token given");
            }else{
                return $this->redirectToRoute('LoginForm');
            }   
        } 
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $site_token], ['limit' => 1]);
        if(!$Site){
            // Invalid token
            $LoggerManager->error($request, $site_token);
            $this->addFlash('login-error', "Invalid token");
            
            if($isAjax){
                // Return json error
                return $this->jsonError("Invalid token");
            }else{
                return $this->redirectToRoute('LoginForm');
            }   
        }
    
        // Check if the CSRF token is valid
        if(!$AuthenticationManager->csrf_check('csrf_token', $request->request->all(), 60*10, false)){
            // Invalid CSRF token
            $LoggerManager->error($request, $site_token);
            $this->addFlash('login-error', $AuthenticationManager->error);
            
            if($isAjax){
                // Return json error
                return $this->jsonError('Invalid CSRF token');
            }else{
                return $this->redirectToRoute('LoginForm');
            }   
        }
        
        // Now perform error checks on user inputs
        $numErrors = 0;
        
        $UsernameOrEmail = $request->request->get('UsernameOrEmail');
        $Password = $request->request->get('Password');
        
        $User = $UsersManager->verifyCredentials($UsernameOrEmail, $Password, $Site);
        if(!$User){
            $LoggerManager->error($request, $site_token);
            $this->addFlash('login-error', $UsersManager->getError()); // Development only. SECURITY RISK
            $numErrors++; // Increment the error count
        }
        
        // Extra log in checks eg brute force
        
        if($numErrors == 0){
            // No errors
            
            // Set the appropriate session variables
            $session = $this->get('session');
            $session->set('User', $User);
            $session->set('LoginString', hash('sha512', $User->getPassword().$_SERVER['HTTP_USER_AGENT']));
            
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
