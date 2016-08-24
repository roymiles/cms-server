<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

class UserManagementController extends Controller implements iTable
{  
    
    public function isColumn(string $columnName, string $flags){
        $columns = ['id', 'username', 'email'];
        if(in_array($columnName, $columns)){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @Route("/manage/users", name="ManagementGetUsers")
     * @Route("/manage/users/page{pageNumber}", name="ManagementGetUsersWithPage")
     * @Route("/manage/users/sort={sortBy}-{order}", name="ManagementGetUsersWithSort")
     * @Route("/manage/users/sort={sortBy}-{order}/page{pageNumber}", name="ManagementGetUsersWithSortAndPage")
     * @Route("/manage/users/sort={sortBy}-{order}/searchBy={searchBy}/q={query}/page{pageNumber}", name="ManagementGetUsersWithSortAndSearchAndPage")
     */
    public function getAction(Request $request, $sortBy = "id", $order = "ASC", $searchBy = 'id', $searchQuery = '', $pageNumber = 1){   
        $UsersManager = $this->get('app.UsersManager');
        $SanitizeInputsManager = $this->get('app.SanitizeInputsManager');
        
        if(!$this->isColumn($sortBy, 'notSensitive')){
            $sortBy = 'id';
        }
        
        // If not a valid and non sensitive search column, search by id
        if($request->get('_route') == "ManagementGetUsersWithSortAndSearchAndPage" && !$this->isColumn($searchBy, 'notSensitive')){
            $searchBy = 'id';
        }
        
        $order = $SanitizeInputsManager->getValidOrder($order);
        
        $Options = []; // No search criteria
        $Filters = ['sortBy' => $sortBy, 'order' => $order, 'limit' => 10, 'offset' => 0]; //Show 10 at a time
        $Users = $UsersManager->get($Options, $Filters);
        
        //echo "Sort By: " . $sortBy . " Page Number: " . $pageNumber;
        return $this->render('default/manage/users.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'users' => $Users,
            'filters' => $Filters,
            'currentPage' =>$pageNumber
        ]);
    }
    
    public function addAction(array $item){
        
    }
    
    /**
     * @Route("/manage/users/delete", name="ManagementDeleteUser")
     */
    public function deleteAction(Request $request){
        $UsersManager = $this->get('app.UsersManager');
        $JsonManager = $this->get('app.JsonManager');
        
        $id = $request->request->get('id');
        
        $Options = ['id' => $id];
        $Filters = ['limit' => 1];
        $User = $UsersManager->get($Options, $Filters);

        $UsersManager->delete($User);
        
        echo $request->request->all();
        return $JsonManager->success('Deleted successfully');        
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
