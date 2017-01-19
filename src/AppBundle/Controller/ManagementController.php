<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Sites;

use AppBundle\Exception\NoSiteTokenSupplied;
use AppBundle\Exception\InvalidSiteToken;
use AppBundle\Exception\AuthorisationError;

class ManagementController extends Controller
{

    /**
     * This is the homepage for the user to manage their site
     * @Route("/manage", name="Manage")
     */
    public function manageAction(Request $request)
    {
        $SitesManager = $this->get('app.SitesManager');

        /*
         *  Check if a token has been supplied
         */
        $SiteToken = $request->query->get('site_token');
        if($SiteToken ===  null){
            throw new NoSiteTokenSupplied(
                'No site token supplied'
            );
        }  

        /*
         *  Validate the site token
         */
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site){
            throw new InvalidSiteToken(
                'Invalid site token'
            );
        }
        
        /*
         * Check the user is allowed to GET the site details. See SiteVoter.php
         */
        $SiteType = new Sites();
        $SiteType->setToken($SiteToken);
        if(!$this->isGranted('GET', $SiteType)){
            throw new AuthorisationError(
                'You are not granted to perform this action'
            );
        }
        /*
         *  TODO:
         *  Check if website is verified
         *  Show a series of links for managing their website
         */
        return $this->render('default/manage/index.html.twig',[
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    
    /**
     * Show all modules available and allow user to enable/disable for their website
     * @Route("/manage/modules", name="manage_modules")
     */
    public function manageModulesAction(Request $request){}
    
    /**
     * Show their websites status. Is it verified? Regenerate tokens
     * @Route("/manage/status", name="ManageStatus")
     */
    public function manageStatusAction(Request $request){}
    
}
