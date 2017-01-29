<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

class AjaxFrameworkController extends Controller
{
    /**
     * Load the client side js framework with the appropriate modules for the site
     * @Route("/api/site_token={site_token}", name="ApiFramework")
     */
    public function apiFrameworkAction(Request $request, $site_token)
    {
        $SitesManager = $this->get('app.SitesManager');
        $SiteModulesManager = $this->get('app.SiteModulesManager');
        
        //$Site = $SitesManager->get(['Token' => $site_token], ['limit' => 1]);
        //$SiteModules = $SiteModulesManager->get(['Site' => $Site], ['limit' => 1]);
        
        // Return a list of all the modules
        //$Modules = $SiteModules->getModules($token);
        
        // Get an array of the module names
        // ...
    
        //$response->headers->set('Content-Type', 'application/javascript');
        
        return $this->render('default/api/client.js.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            //'modules' => $Modules
        ]);
    }
    
    
    
}
