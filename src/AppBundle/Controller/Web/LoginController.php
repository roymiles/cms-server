<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Sites;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="Login")
     */
    public function loginAction(Request $request)
    {
        /*
         *  Symfony automatically starts sessions for you
         *  http://stackoverflow.com/questions/21276048/failed-to-start-the-session-already-started-by-php-session-is-set-500-inte
         */
        $session = $request->getSession();
        
        $SiteToken = $request->query->get('site_token', 'a');
        if($SiteToken ===  null){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "No token supplied"
            ]);
        }  
        
        $SitesManager = $this->get('app.SitesManager');
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "Site token does not correspond to a site"
            ]);
        }
        
        if($Site->getId() != -1){
            // External website login
            return $this->render('default/api/pages/login.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'site_token' => $SiteToken,
                'site' => $Site
            ]);
        }
        
        $user = $this->getUser();
        if($user instanceof UserInterface){
            return $this->redirectToRoute('Homepage');
        }

        /** @var AuthenticationException $exception */
        $exception = $this->get('security.authentication_utils')
          ->getLastAuthenticationError();
        
        
        //$csrf_token = $AuthenticationManager->csrf_generate('csrf_token');
        return $this->render('default/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'login',
            //'csrf_token' => $csrf_token,
            'site_token' => $SiteToken,
            
            'session_data' => $session->all(),
            'error' => $exception ? $exception->getMessage() : NULL,
        ]);
    }
}
