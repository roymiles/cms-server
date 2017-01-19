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
         * 
         *  Retrieve the session object
         */
        $session = $request->getSession();
        
        $local_site_token = $this->container->getParameter('local_site_token');
        /*
         *  If a site token is not supplied, use the local site token for
         *  a local login
         */
        $SiteToken = $request->query->get('site_token', $local_site_token);
        
        /*
         * Check if the site token isn't null (may be redundant due to default
         * value)
         */
        if($SiteToken ===  null){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "No token supplied"
            ]);
        }  
        
        /*
         * Validate the site token
         */
        $SitesManager = $this->get('app.SitesManager');
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "Site token does not correspond to a site"
            ]);
        }
        
        if($Site->getId() != -1){
            /*
             *  External website login (site id = -1)
             */
            return $this->render('default/api/pages/login.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'site_token' => $SiteToken,
                'site' => $Site
            ]);
        }
        
        /*
         * If user is already logged in, return to the homepage
         */
        $user = $this->getUser();
        if($user instanceof UserInterface){
            return $this->redirectToRoute('Homepage');
        }

        /** @var AuthenticationException $exception */
        $exception = $this->get('security.authentication_utils')
          ->getLastAuthenticationError();
        
        /*
         *  Absolute url for which user will be redirected to after success
         */
        $redirect = $request->query->get('redirect', null);
        
        //$csrf_token = $AuthenticationManager->csrf_generate('csrf_token');
        return $this->render('default/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'login',
            //'csrf_token' => $csrf_token,
            'site_token' => $SiteToken,
            
            'redirect' => $redirect,
            'session_data' => $session->all(),
            'error' => $exception ? $exception->getMessage() : NULL,
        ]);
    }
}
