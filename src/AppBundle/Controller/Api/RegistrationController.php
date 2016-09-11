<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/api/register", name="ApiRegisterForm")
     */
    public function registerFormAction(Request $request)
    {
    }
    
    /**
     * @Route("/api/processRegister", name="ApiProcessRegisterRequest")
     */
    public function processRegisterRequestAction(Request $request)
    {  
    }
}
