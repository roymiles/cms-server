<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $SitesManager = $this->get('app.SitesManager');
        
        // Retrieve and define default filter parameters from $_GET vars (if not supplied)
        $pageNumber = $request->query->get('pageNumber', 1);
        $sortBy = $request->query->get('sortBy', 'Id');    
        $order = $request->query->get('order', 'ASC');

        // Is there a token in the URL?
        $SiteToken = $request->query->get('site_token');
        if($SiteToken ===  null){
            return new JsonResponse(array('Error' => 'No site token supplied'), Response::HTTP_NOT_FOUND);
        }  

        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site){
            return new JsonResponse(array('Error' => 'Site token is not valid'), Response::HTTP_NOT_FOUND);
        }
        $SiteID = $Site->getID();
        
        $UsersManager = $this->get('app.UsersManager');
        $SanitizeInputsManager = $this->get('app.SanitizeInputsManager');
          
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
        
        // Validate the order parameter (will convert ascending -> ASC etc)
        $order = $SanitizeInputsManager->getValidOrder($order);
        
        $Options = [];
        $Filters = ['Site' => $SiteID, 'sortBy' => $sortBy, 'order' => $order, 'limit' => 10, 'offset' => 0]; //Show 10 at a time
        $Users = $UsersManager->get($Options, $Filters);
        
        if(!$this->isGranted('GET', $Users)){
            return new JsonResponse(array('Error' => 'You are not granted to perform this action'), Response::HTTP_FORBIDDEN);
        }
        
        $SerializedUserArray = array();
        foreach($Users as $User){
            array_push($SerializedUserArray, $UsersManager->serialize($User));
        }
        return new JsonResponse(array('Users' => $SerializedUserArray), Response::HTTP_OK);
    }
}
