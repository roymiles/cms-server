<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Sites;

class LoginController extends Controller
{
    /**
     * @Route("/api/login", name="ApiLogin")
     */
    public function loginFormAction(Request $request)
    {
        die('THIS IS REDUNDANT, SEE WEB LOGIN CONTROLLER');
        // Render the form which can be loaded into external pages via ajax
        $SiteToken = $request->query->get('site_token', null);
        if($SiteToken ===  null){
            return new JsonResponse(array('Error' => 'No site token supplied'), Response::HTTP_NOT_FOUND);
        }  
        
        $SitesManager = $this->get('app.SitesManager');
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            return new JsonResponse(array('Error' => 'Site token does not correspond to a site'), Response::HTTP_NOT_FOUND);
        }
        
        // Cant login to the local site
        if($Site->getId() == -1){
            return new JsonResponse(array('Error' => 'Cant login to the local site'), Response::HTTP_FORBIDDEN); 
        }
        
        return $this->render('default/api/pages/login.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'site_token' => $SiteToken
        ]);
    }
    
    /**
     * @Route("/api/processLogin", name="ApiProcessLoginRequest")
     * @Method({"POST"})
     */
    public function processLoginRequestAction(Request $request)
    {
        die('THIS IS REDUNDANT, SEE WEB LOGIN CONTROLLER');
        $AuthenticationManager = $this->get('app.AuthenticationManager');
        $UsersManager = $this->get('app.UsersManager');
        
        // Is there a token in the URL
        $SiteToken = $request->request->get('site_token');
        
        if($SiteToken ===  null){
            // No Token supplied
            return new JsonResponse(array('Error' => 'No site token supplied'), Response::HTTP_FORBIDDEN);   
        }  
        
        $SitesManager = $this->get('app.SitesManager');
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            return new JsonResponse(array('Error' => 'Site token does not correspond to a site'), Response::HTTP_NOT_FOUND); 
        }
        
        if($Site->getId() != -1){
            // Cant login to the local site through the api
            return new JsonResponse(array('Error' => 'Cant login to the local site through the api'), Response::HTTP_FORBIDDEN);
        }
    }
}
