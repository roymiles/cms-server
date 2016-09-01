<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="RegisterForm")
     */
    public function registerFormAction(Request $request)
    {
        /*
         *  Symfony automatically starts sessions for you
         *  http://stackoverflow.com/questions/21276048/failed-to-start-the-session-already-started-by-php-session-is-set-500-inte
         */
        $session = $request->getSession();
        
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        
        $csrf_token = $AuthenticationManager->csrf_generate('csrf_token');
        return $this->render('default/pages/register.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'register',
            'csrf_token' => $csrf_token,
        ]);
    }
    
    /**
     * @Route("/processRegister", name="ProcessRegisterRequest")
     */
    public function processRegisterRequestAction(Request $request)
    {
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        $UsersManager = $this->get('app.UsersManager');
        $SitesManager = $this->get('app.SitesManager');
        $LoggerManager = $this->get('app.LoggerManager');
        
        // Check if the site token is local or external
        $isAjax = $request->isXmlHttpRequest();
        
        // Is there a token in the URL
        $site_token = $request->request->get('site_token');
        
        $UserDetails = [];
        $UserDetails['Username'] = $request->request->get('Username');
        $UserDetails['Email'] = $request->request->get('Email');
        
        $InvitationCode = $request->request->get('InvitationCode');
        $Password = $request->request->get('Password');
        $RepeatPassword = $request->request->get('RepeatPassword');
        
        /*
         *  Set flash variables to populate registration form on redirect (if there is an error)
         *  - Sanitizing these variables to prevent XSS
         */
        $this->addFlash('registration-form-submitted-username', htmlentities($UserDetails['Username']));
        $this->addFlash('registration-form-submitted-email', htmlentities($UserDetails['Email']));
        $this->addFlash('registration-form-submitted-invitationcode', htmlentities($InvitationCode));
        // Don't include the password in the flash variables!
        
        if($site_token ===  null){
            // No Token supplied
            $LoggerManager->error($request, $site_token);
            $this->addFlash('registration-error', "No token given");
            
            if($isAjax){
                // Return json error
                return $this->jsonError("No token given");
            }else{
                return $this->redirectToRoute('RegisterForm');
            }   
        } 
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $site_token], ['limit' => 1]);
        if(!$Site){
            // Invalid token
            $LoggerManager->error($request, $site_token);
            $this->addFlash('registration-error', "Invalid token");
            
            if($isAjax){
                // Return json error
                return $this->jsonError("Invalid token");
            }else{
                return $this->redirectToRoute('RegisterForm');
            }   
        }
    
        // Check if the CSRF token is valid
        if(!$AuthenticationManager->csrf_check('csrf_token', $request->request->all(), 60*10, false)){
            // Invalid CSRF token
            $LoggerManager->error($request, $site_token);
            $this->addFlash('registration-error', $AuthenticationManager->error);
            
            if($isAjax){
                // Return json error
                return $this->jsonError('Invalid CSRF token');
            }else{
                return $this->redirectToRoute('RegisterForm');
            }   
        }
        
        // Now perform error checks on user inputs
        $numErrors = 0;
        
        // Check if passwords match
        if($Password !== $RepeatPassword){
            $LoggerManager->error($request, $site_token);
            $this->addFlash('registration-error', 'Passwords do not match');
            $numErrors++;   
        }
        
        // Extra log in checks eg brute force
        
        if($numErrors == 0){
            // No errors
            
            // Attempt to add the user
            if(!$UsersManager->add($UserDetails)){
                // Validation error
                $ValidationErrorString = $UsersManager->getValidationErrorString();
                $this->addFlash('registration-error', $ValidationErrorString);
                return $this->redirectToRoute('RegisterForm');
            }else{           
                // Set a flash variable saying account has been created
                return $this->redirectToRoute('LoginForm');
            }
            
        }else{
            if($isAjax){
                // Return json error
            }else{
                return $this->redirectToRoute('RegisterForm');
            }
        }
    }    
}
