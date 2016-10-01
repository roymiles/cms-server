<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

use AppBundle\Entity\Users;
use AppBundle\Forms\UserType;

use AppBundle\Exception\NoSiteTokenSupplied;
use AppBundle\Exception\InvalidSiteToken;
use AppBundle\Exception\AuthorisationError;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     * @ApiDoc(
     *  description="Returns a collection of Object",
     *  requirements={
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="how many objects to return"
     *      }
     *  },
     *  parameters={
     *      {"name"="categoryId", "dataType"="integer", "required"=true, "description"="category id"}
     *  }
     * )
     * 
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
            throw new NoSiteTokenSupplied(
                'No site token supplied'
            );
        }  

        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $SiteToken], ['limit' => 1]);
        if(!$Site){
            throw new InvalidSiteToken(
                'Invalid site token'
            );
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
        
        $UserType = new Users();
        $UserType->setSite($Site);
        if(!$this->isGranted('GET', $UserType)){
            throw new AuthorisationError(
                'You are not granted to perform this action'
            );
        }
        
        $usersPerPage = 10;        
        $totalResults = $UsersManager->count(['SiteId' => $Site->getId()]);
        $lastPage = ceil($totalResults / $usersPerPage);    
        
        // See: http://stackoverflow.com/questions/3520996/calculating-item-offset-for-pagination
        $offset = ($pageNumber - 1) * $usersPerPage + 1;
        
        $Options = [];
        $Filters = ['sortBy' => $sortBy, 'order' => $order, 'limit' => $usersPerPage, 'offset' => $offset, 'Site' => $Site];
        $Users = $UsersManager->get($Options, $Filters);        
        
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
            // Add new users form
            $User = new Users();
            $form = $this->createForm(UserType::class, $User, array('action' => 'whatever you want'));
            
            // Create breadcrumb links
            $breadcrumbs = array();
            array_push($breadcrumbs, array('active' => false, 'path' => 'Manage', 'name' => 'Home'));
            array_push($breadcrumbs, array('active' => true, 'path' => 'ManagementGetUsers', 'name' => 'Users'));            
            
            return $this->render('default/manage/users.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'activeTab' => 'manage',
                'users' => $Users,
                'routeFilters' => $routeFilters,
                'addUserForm' => $form->createView(),
                'lastPage' => $lastPage, // Used for pagination
                'totalResults' => $totalResults, // Used to display to user how many results returned
                'breadcrumbs' => $breadcrumbs // Links for the breadcrumbs at the top
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
        $SitesManager = $this->get('app.SitesManager');
        
        /*
         *  Is there a token in the URL?
         *  This is needed for redirect to user management console and
         *  as extra verification. Check if user matches the site
         */
        $token = $request->query->get('site_token');
        if($token ===  null){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "No token supplied"
            ]);
        } 
        
        // Is there a user id in the URL?
        $UserId = $request->query->get('UserId');
        if($UserId ===  null){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "No user supplied"
            ]);
        }  
        
        $User = $UsersManager->get(['Id' => $UserId], ['limit' => 1]);
        if(!$User instanceof Users){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "User not found"
            ]);
        }
        
        // Does the token correspond to a valid site
        $Site = $SitesManager->get(['Token' => $token], ['limit' => 1]);
        if(!$Site){
            return $this->render('default/blank.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'content' => "Invalid site token"
            ]);
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
