<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

use AppBundle\Entity\Users;
use AppBundle\Forms\UserType;

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
        $SiteToken = $request->query->get('site_token');
        if($SiteToken ===  null){
            return $this->render('default/error-response.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'error' => "No token supplied"
            ]);
        }  

        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site){
            return $this->render('default/error-response.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'error' => "Invalid token"
            ]);
        }
        
        $UsersManager = $this->get('app.UsersManager');
        $SanitizeInputsManager = $this->get('app.SanitizeInputsManager');
          
        // Validate the sortBy parameter   
        if(!$this->isColumn($sortBy, 'notSensitive')){
            $sortBy = 'Id';
        }
        
        // Validate the searchBy $_GET parameter 
        /*$searchBy = $request->query->get('searchBy');  
        $searchQuery = $request->query->get('q');  
        if($this->isColumn($searchBy, 'notSensitive')){
            $searchBy = 'Username';
        }*/
        
        // Validate the order parameter (will convert ascending -> ASC etc)
        $order = $SanitizeInputsManager->getValidOrder($order);
        
        $usersPerPage = 10;        
        $totalResults = $UsersManager->count($Site->getId());
        $lastPage = ceil($totalResults / $usersPerPage);    
        
        // See: http://stackoverflow.com/questions/3520996/calculating-item-offset-for-pagination
        $offset = ($pageNumber - 1) * $usersPerPage + 1;
        
        $Options = [];
        $Filters = ['sortBy' => $sortBy, 'order' => $order, 'limit' => $usersPerPage, 'offset' => $offset];
        $Users = $UsersManager->get($Options, $Filters);
        
        if(!$this->isGranted('GET', $Users)){
            return $this->render('default/error-response.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'error' => "You are not granted to perform this action"
            ]);
        }
        
        // Validate the pageNumber parameter
        // ...
        
        $routeFilters = ['sortBy' => $sortBy, 'order' => $order, 'site_token' => $SiteToken, 'pageNumber' => $pageNumber];
        
        if($request->query->has('searchBy')){
            $routeFilters['searchBy'] =$request->query->get('searchBy');
        }
        
        if($request->query->has('q')){
            $routeFilters['q'] =$request->query->get('q');
        }        
        
        if($request->isXmlHttpRequest()){
            // AJAX request
            return new JsonResponse([
                'users' => $Users,
                'routeFilters' => $routeFilters
            ]);
        }else{
            $User = new Users();
            $form = $this->createForm(UserType::class, $User, array('action' => 'whatever you want'));
            
            return $this->render('default/manage/users.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'activeTab' => 'manage',
                'users' => $Users,
                'routeFilters' => $routeFilters,
                'addUserForm' => $form->createView(),
                'lastPage' => $lastPage
            ]);
        }
    }
    
    public function addAction(array $item){
        
    }
    
    /**
     * @Route("/manage/users/delete", name="ManagementDeleteUser")
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
        $token = $request->query->get('site_token');
        if($token ===  null){
            return $ErrorResponsesManager->nullToken($request, $token);
        } 
        
        // Is there a user id in the URL?
        $UserId = $request->query->get('UserId');
        if($UserId ===  null){
            return $ErrorResponsesManager->nullParameter($request, 'User Id');
        }  
        
        $User = $UsersManager->get(['Id' => $UserId], ['limit' => 1]);
        if(!$User instanceof Users){
            return $ErrorResponsesManager->noUserFound($request);
        }
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $token], ['limit' => 1]);
        if(!$Site){
            return $ErrorResponsesManager->invalidToken($request, $token);
        }
        
        if(!$this->isGranted('DELETE', $User)){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "You are not granted to perform this action"
            ]);
        }
        
        $Options = ['Id' => $User->getId()];
        $Filters = ['limit' => 1];
        $User = $UsersManager->get($Options, $Filters);

        $UsersManager->delete($User);
        
        if($request->isXmlHttpRequest()){
            // AJAX request
            return new JsonResponse([
                'success' => 1
            ]);
        }else{
            $this->addFlash(
                'banner-notice',
                'User deleted successfully'
            );
            return $this->redirect($this->generateUrl('ManagementGetUsers', array('site_token' => $token)));
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
