<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ForgotPasswordController extends Controller
{
    /**
     * @Route("/forgotPassword", name="ForgotPasswordForm")
     */
    public function forgotPasswordFormAction(Request $request)
    {
        return $this->render('default/pages/404.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    
    /**
     * @Route("/processForgotPasswordRequest", name="ProcessForgotPasswordRequest")
     */
    public function processForgotPasswordRequestAction(Request $request)
    {
        return $this->render('default/pages/404.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }    
}
