<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Sites;
use AppBundle\Entity\Users;
use AppBundle\Forms\UserType;

class RegistrationController extends Controller
{
    /**
     * @Route("/api/register", name="ApiRegisterForm")
     */
    public function registerFormAction(Request $request)
    {
        // Render the form which can be loaded into external pages via ajax
        $response = new JsonResponse();
        $SiteToken = $request->query->get('site_token', null);
        if($SiteToken ===  null){
            $response->setData(array(
                'error' => "No token supplied"
            ));
            return $response;
        }  
        
        $SitesManager = $this->get('app.SitesManager');
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            $response->setData(array(
                'error' => "Token does not correspond to a site"
            ));
            return $response;   
        }
        
        // Cant login to the local site
        if($Site->getId() == -1){
            $response->setData(array(
                'error' => "Cant login to the local site"
            ));
            return $response;  
        }
        
        $User = new Users();
        $form = $this->createForm(UserType::class, $User, array('action' => 'whatever you want'));
        
        return $this->render('default/api/pages/register.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'site_token' => $SiteToken,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/api/processRegister", name="ApiProcessRegisterRequest")
     */
    public function processRegisterRequestAction(Request $request)
    {  
    }
}
