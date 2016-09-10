<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="Login")
     */
    public function loginFormAction(Request $request)
    {
        /*
         *  Symfony automatically starts sessions for you
         *  http://stackoverflow.com/questions/21276048/failed-to-start-the-session-already-started-by-php-session-is-set-500-inte
         */
        $session = $request->getSession();
        
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        
        $user = $this->getUser();
        if($user instanceof UserInterface){
            return $this->redirectToRoute('Homepage');
        }

        /** @var AuthenticationException $exception */
        $exception = $this->get('security.authentication_utils')
          ->getLastAuthenticationError();
        
        
        $csrf_token = $AuthenticationManager->csrf_generate('csrf_token');
        return $this->render('default/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'login',
            'csrf_token' => $csrf_token,
            
            'session_data' => $session->all(),
            'error' => $exception ? $exception->getMessage() : NULL,
        ]);
    }
    
    /**
     * @Route("/processLogin", name="ProcessLoginRequest")
     * @Method({"POST"})
     */
    /*public function processLoginRequestAction(Request $request)
    {
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        $UsersManager = $this->get('app.UsersManager');
        $SitesManager = $this->get('app.SitesManager');
        $LoggerManager = $this->get('app.LoggerManager');
        
        // Check if the site token is local or external
        $isAjax = $request->isXmlHttpRequest();
        
        // Is there a token in the URL
        $site_token = $request->request->get('site_token');
        $csrf_token = $request->request->get('csrf_token');
        
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
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $site_token], ['limit' => 1]);
        if(!$Site){
            // Invalid token
            $LoggerManager->error('Invalid site token', ['site_token' => $site_token]);
            $this->addFlash('loginErrors', "Invalid token");
            
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
            $LoggerManager->error('Invalid csrf token', ['csrf_token' => $csrf_token]);
            $this->addFlash('loginErrors', $AuthenticationManager->error);
            
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
            if($isAjax){
                      
            }else{
                $LoggerManager->error('Invalid credentials', ['UsernameOrEmail' => $UsernameOrEmail, 'Password' => $Password]);
                $Errors = $UsersManager->getErrors();
                foreach($Errors as $Error){
                    // addFlash pushes each element into an array
                    $this->addFlash('loginErrors', $Error);
                }

                $numErrors++; // Increment the error count
            }
        }
        
        if($isAjax){
            // Check if valid redirect
        }
        
        // Extra log in checks eg brute force
        
        if($numErrors == 0){
            // No errors
            
            if($isAjax){
                
            }else{
                // Set the appropriate session variables
                $session = $this->get('session');
               // dump($User);die();
                $session->set('User', $User);
                $session->set('isLoggedIn', true);
                $session->set('LoginString', hash('sha512', $User->getPassword().$_SERVER['HTTP_USER_AGENT']));

                return $this->redirectToRoute('Homepage');
            }
        }else{
            if($isAjax){
                // Return json error
            }else{
                return $this->redirectToRoute('LoginForm');
            }
        }
    } */   
}
