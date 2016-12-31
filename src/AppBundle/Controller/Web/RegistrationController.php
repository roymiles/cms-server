<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Users;
use AppBundle\Forms\UserType;

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
        
        //$AuthenticationManager = $this->get('app.AuthenticationManager');     
        //$csrf_token = $AuthenticationManager->csrf_generate('csrf_token');
        
        $User = new Users();
        // Generate the form for the UserType. Include username, email, password
        $form = $this->createForm(UserType::class, $User, array('action' => $this->generateUrl('ProcessRegisterRequest')));
        
        return $this->render('default/pages/register.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'register',
            'csrf_token' => $csrf_token,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/processRegister", name="ProcessRegisterRequest")
     */
    public function processRegisterRequestAction(Request $request)
    {
        $AuthenticationManager  = $this->get('app.AuthenticationManager');
        $UsersManager           = $this->get('app.UsersManager');
        $SitesManager           = $this->get('app.SitesManager');
        $ValidationManager      = $this->get('app.ValidationManager');
        
        /*
         *  Get user form fields
         *  eg:
            "user" => array:4 [▼
                "Username" => "example"
                "Email" => "example@example.com"
                "Password" => "password"
                "_token" => "GkmZQRZUOWvn80XuPUVcOeUDBmSskdJN2LAIshZcboA"
            ]
         * 
         */
        $user           = $request->request->get('user');
        $repeatPassword = $request->request->get('repeatPassword');
        
        // Check if all field elements exist, if not redirect back to register form
        if(['Username', 'Email', 'Password', '_token'] != array_keys($user)){
            $this->addFlash('registrationErrors', "Malformed request");
            return $this->redirectToRoute('RegisterForm');
        }
        
        // Is there a token in the URL
        $site_token = $request->request->get('site_token');
        
        /*
         *  Set flash variables to populate registration form on redirect (if there is an error)
         *  - Sanitizing these variables to prevent XSS
         *  - Don't include the password in the flash variables!
         */
        $this->addFlash('submittedUsername', htmlentities($user['Username']));
        $this->addFlash('submittedEmail', htmlentities($user['Email']));
        
        /*
         *  Check to see if the site_token is valid
         *  - If there are no errors, site_token will return the site
         */
        $Site = $ValidationManager->site_token($site_token);
        
        $errors = $ValidationManager->getErrors();
        foreach($errors as $error){
            // addFlash pushes each element into an array
            $this->addFlash('registrationErrors', $error);
        }        
        
        // If there are errors in the site_token, redirect immediately
        if(!empty($errors)){
            return $this->redirectToRoute('RegisterForm');
        }
        
        // Set the Site of the User
        $user['Site'] = $Site;
        
        $ValidationManager->username($user['Username'], true, $Site);   
        $ValidationManager->email($user['Email'], true, $Site);   
        $ValidationManager->password($user['Password'], ['username' => $user['Username'], 'password' => $user['Password']], $repeatPassword);        
        
        $errors = array_merge($errors, $ValidationManager->getErrors());
        foreach($errors as $error){
            // addFlash pushes each element into an array
            $this->addFlash('registrationErrors', $error);
        }
        
        // If there are errors in the fields (will be empty error if none)
        if(!empty($errors)){
            return $this->redirectToRoute('RegisterForm');
        }

        // Passed all validation, so add the user
        $UsersManager->add($user);
        
        // Redirect to the login form so that the user can log in with their new account
        return $this->redirectToRoute('LoginForm');
           
    }    
}
