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
     * @Route("/api/login", name="ApiLogin")
     */
    public function loginFormAction(Request $request)
    {
    }
    
    /**
     * @Route("/api/processLogin", name="ApiProcessLoginRequest")
     * @Method({"POST"})
     */
    public function processLoginRequestAction(Request $request)
    {
    }
}
