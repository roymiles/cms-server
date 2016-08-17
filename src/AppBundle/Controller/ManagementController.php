<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ManagementController extends Controller
{

    /**
     * @Route("/manage", name="Manage")
     */
    public function manageAction(Request $request)
    {
        // Check if logged in
        // Check if own a site with framework
        // Is their website verified?
        // Show a series of links for managing their website
        return $this->render('default/manage/index.html.twig',[
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    
    
    /**
     * @Route("/manage/users", name="ManageUsers")
     */
    public function manageUsersAction(Request $request){}
    
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
