<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sites;

class FakerController extends Controller
{   
    /**
     * @Route("/faker/users", name="FakerUsers")
     */
    public function usersAction(Request $request)
    {
        $SitesManager = $this->get('app.SitesManager');
        
        // Is there a token in the URL?
        $SiteToken = $request->query->get('site_token');
        if($SiteToken ===  null){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "No token supplied"
            ]);
        }        

        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "Invalid token"
            ]);
        }   

        $UsersManager = $this->get('app.UsersManager');
        require_once dirname(__FILE__).'/../Libraries/Faker/autoload.php';
        $faker = \Faker\Factory::create();
        
        $numEntries = $request->query->get('numEntries', 5);
        
        for($i = 0; $i < $numEntries; $i++){
            $Options = array();
            $Options['Username'] = $faker->username;
            $Options['Email'] = $faker->email;
            $Options['Password'] = $faker->password;
            $Options['Site'] = $Site;
            $Options['isVerified'] = 1;
            
            $UsersManager->add($Options);
        }
        
        if($request->isXmlHttpRequest()){
            // AJAX request
            return new JsonResponse([
                'success' => 1
            ]);
        }else{
            $this->addFlash(
                'banner-notice',
                "Added " . $numEntries . " users into the database"
            );
            return $this->redirect($this->generateUrl('ManagementGetUsers', array('site_token' => $SiteToken)));
        }         
    }
    
    /**
     * @Route("/faker/sites", name="FakerSites")
     */
    public function sitesAction(Request $request)
    {
        $SitesManager = $this->get('app.SitesManager');  
        $UsersManager = $this->get('app.UsersManager');
        
        require_once dirname(__FILE__).'/../Libraries/Faker/autoload.php';
        $faker = \Faker\Factory::create();
        
        // Make the user "admin" the owner of the randomly generated site
        $User = $UsersManager->get(['Id' => 1], ['limit' => 1]);
        
        $numEntries = $request->query->get('numEntries', 5);
        
        for($i = 0; $i < $numEntries; $i++){
            $Options = array();
            $Options['Owner'] = $User;
            $Options['DomainName'] = $faker->domainName;
            
            $SitesManager->add($Options);
        }
        
        return $this->render('default/blank.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'content' => "Added " . $numEntries . " sites into the database"
        ]);
    }    
}
