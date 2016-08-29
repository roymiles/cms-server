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
        return $this->render('default/pages/register.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'register',
        ]);
    }
    
    /**
     * @Route("/processRegister", name="ProcessRegisterRequest")
     */
    public function processRegisterRequestAction(Request $request)
    {
        return $this->render('default/pages/register.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }    
}
