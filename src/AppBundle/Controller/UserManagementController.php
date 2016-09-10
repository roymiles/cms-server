<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

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
     * @Route("/manage/users", name="ManagementGetUsers")
     */
    public function getAction(Request $request){
        $SitesManager = $this->get('app.SitesManager');
        
        // Retrieve and define default filter parameters from $_GET vars (if not supplied)
        $pageNumber = $request->query->get('pageNumber', 1);
        $sortBy = $request->query->get('sortBy', 'Id');    
        $order = $request->query->get('order', 'ASC');

        // Is there a token in the URL?
        $token = $request->query->get('token');
        if($token ===  null){
            return $this->render('default/error-response.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'ErrorResponse' => "No token supplied"
            ]);
        }  

        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $token], ['limit' => 1]);
        if(!$Site){
            return $this->render('default/error-response.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'ErrorResponse' => "Invalid token"
            ]);
        }
        
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
        $Filters = ['sortBy' => $sortBy, 'order' => $order, 'limit' => 10, 'offset' => 0]; //Show 10 at a time
        $Users = $UsersManager->get($Options, $Filters);
        
        if(!$this->isGranted('GET', $Users)){
            return $this->render('default/error-response.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'ErrorResponse' => "You are not granted to perform this action"
            ]);
        }
        
        // Validate the pageNumber parameter
        // ...
        
        $routeFilters = ['sortBy' => $sortBy, 'order' => $order, 'token' => $token, 'pageNumber' => $pageNumber];
        
        if($request->isXmlHttpRequest()){
            // AJAX request
            return new JsonResponse([
                'users' => $Users,
                'routeFilters' => $routeFilters
            ]);
        }else{
            return $this->render('default/manage/users.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'users' => $Users,
                'routeFilters' => $routeFilters
            ]);
        }
    }
    
    public function addAction(array $item){
        
    }
    
    /**
     * @Route("/token={token}/manage/users/delete/UserId={UserId}", name="ManagementDeleteUser")
     */
    public function deleteAction(Request $request){
        $UsersManager = $this->get('app.UsersManager');     
        $ErrorResponsesManager = $this->get('app.ErrorResponsesManager');
        $SitesManager = $this->get('app.SitesManager');
        
        /*
         *  Is there a token in the URL?
         *  This is needed for redirect to user management console and
         *  as extra verification. Check if user matches the site
         */
        $token = $request->attributes->get('token');
        if($token ===  null){
            return $ErrorResponsesManager->nullToken($request, $token);
        } 
        
        // Is there a user id in the URL?
        $UserId = $request->attributes->get('UserId');
        if($UserId ===  null){
            return $ErrorResponsesManager->nullParameter($request, 'User Id');
        }  
        
        $Users = $UsersManager->get(['Id' => $UserId], ['limit' => 1]);
        if(!$Users){
            return $ErrorResponsesManager->noUserFound($request);
        }
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $token], ['limit' => 1]);
        if(!$Site){
            return $ErrorResponsesManager->invalidToken($request, $token);
        }
        
        var_dump($Users->getId());
        var_dump($Site->getId());
        
        //$Options = ['id' => $id];
        //$Filters = ['limit' => 1];
        //$User = $UsersManager->get($Options, $Filters);

        //$UsersManager->delete($User);
        
        if($request->isXmlHttpRequest()){
            // AJAX request
            return new JsonResponse([
                'success' => 1
            ]);
        }else{
            $this->addFlash(
                'notice',
                'User deleted successfully'
            );
            return $this->redirectToRoute($this->generateUrl('ManagementGetUsers', array('token' => $token)));
        }  
    }
    
    /**
     * @Route("/manage/users/update", name="ManagementUpdateUser")
     */
    public function updateAction(Request $request){
        $UsersManager = $this->get('app.UsersManager');
        $JsonManager = $this->get('app.JsonManager');
        
        $id = $request->request->get('id');
        $columnName = $request->request->get('columnName');
        $newValue = $request->request->get('newValue');
        
        $Options = ['id' => $id];
        $Filters = ['limit' => 1];
        $User = $UsersManager->get($Options, $Filters);
        
        if(!$this->isColumn($columnName, 'notSensitive')){
            return $JsonManager->error('Invalid parameter');
        }
        
        $UpdateOptions = [$columnName => $newValue];
        $UsersManager->update($User, $UpdateOptions);
        
        echo $request->request->all();
        return $JsonManager->success('Updated successfully');
    }
}
