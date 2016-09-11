<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;

class LogoutController extends Controller
{
    /**
     * @Route("/logout", name="Logout")
     */
    public function logoutAction(Request $request)
    {
        $session = $this->get('session');
        $session->clear(); // Clear all attributes
        return $this->redirectToRoute('Homepage');
    }
}
