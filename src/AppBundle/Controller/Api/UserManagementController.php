<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Users;
use AppBundle\Entity\Sites;
use AppBundle\Entity\AccessTokens;

class UserManagementController extends Controller
{  
    
    public function isColumn($columnName, string $flags){
        if($columnName === null){ return false; } 
        $columns = ['id', 'username', 'email'];
        if(in_array(strtolower($columnName), $columns)){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @Route("/api/manage/users", name="ApiManagementGetUsers")
     * @Method("GET")
     */
    public function getAction(Request $request){        
        // Retrieve and define default filter parameters from $_GET vars (if not supplied)
        $pageNumber = $request->query->get('pageNumber', 1);
        $sortBy = $request->query->get('sortBy', 'Id');    
        $order = $request->query->get('order', 'ASC');

        // Is there a token in the URL?
        $SiteToken = $request->query->get('site_token');
        if($SiteToken ===  null){
            return new JsonResponse(array('Error' => 'No site token supplied'), Response::HTTP_NOT_FOUND);
        }  
        
        $SitesManager = $this->get('app.SitesManager');

        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site instanceof Sites){
            return new JsonResponse(array('Error' => 'Site token does not correspond to a site'), Response::HTTP_NOT_FOUND);
        }
        
        // Cant access local site data
        if($Site->getId() == -1){
            return new JsonResponse(array('Error' => 'Cant access the local site data directly'), Response::HTTP_FORBIDDEN); 
        }
        
        // Is there an access token
        $AccessToken = $request->query->get('access_token');
        if($AccessToken ===  null){
            return new JsonResponse(array('Error' => 'No access token supplied'), Response::HTTP_FORBIDDEN);
        }  
        
        $AccessTokensManager = $this->get('app.AccessTokensManager');

        // Is the access token valid
        $AccessTokenObject = $AccessTokensManager->get(['Token' => $AccessToken], ['limit' => 1]);
        if(!$AccessTokenObject instanceof AccessTokens){
            return new JsonResponse(array('Error' => 'Access token is not valid'), Response::HTTP_FORBIDDEN);
        }
        
        // Does the access token match the site
        if(!$SitesManager->isEqual($AccessTokenObject->getUser()->getSite(), $Site)){
            return new JsonResponse(array('Error' => 'Access token does not match the request'), Response::HTTP_FORBIDDEN);
        }     
        
        $u = new Users();
        $u->setSite($Site);
        if(!$this->isGranted('GET', $u)){
            return new JsonResponse(array('Error' => 'You are not granted to perform this action'), Response::HTTP_FORBIDDEN);
        }
        
        $UsersManager = $this->get('app.UsersManager');
          
        // Validate the sortBy parameter   
        if(!$this->isColumn($sortBy, 'notSensitive')){
            $sortBy = 'Id';
        }
        
        // Validate the searchBy $_GET parameter 
        $searchBy = $request->query->get('searchBy');  
        $searchQuery = $request->query->get('searchQuery');  
        if($this->isColumn($searchBy, 'notSensitive')){
            $searchBy = 'Username';
        }
        
        $Options = [];
        $Filters = ['Site' => $Site, 'sortBy' => $sortBy, 'order' => $order, 'limit' => 10, 'offset' => 0];
        $Users = $UsersManager->get($Options, $Filters);        
        
        $SerializedUserArray = array();
        foreach($Users as $User){
            array_push($SerializedUserArray, $UsersManager->serialize($User));
        }
        return new JsonResponse(array('Users' => $SerializedUserArray), Response::HTTP_OK);
    }
}
