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
        
        
        
        return $this->render('default/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }    
}
