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
        
        return $this->render('default/blank.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'content' => "Added " . $numEntries . " users into the database"
        ]);
    }
}
