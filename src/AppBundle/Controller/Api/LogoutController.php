<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;

class LogoutController extends Controller
{
    /**
     * @Route("/api/logout", name="ApiLogout")
     */
    public function logoutAction(Request $request)
    {
        // Remove access token...
    }
}
