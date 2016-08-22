<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

class UserManagementController extends Controller implements iTable
{  
    
    /*public function isColumn(string $columnName){
        $columns = ['id', 'username', 'email'];
        if(in_array($columnName, $columns)){
            return true;
        }else{
            return false;
        }
    }*/
    
    /**
     * @Route("/manage/users", name="ManagementGetUsers")
<<<<<<< HEAD
     * @Route("/manage/users/page{pageNumber}", name="ManagementGetUsersWithPage")
     * @Route("/manage/users/sort={sortBy}/{order}", name="ManagementGetUsersWithSort")
     * @Route("/manage/users/sort={sortBy}/{order}/page{pageNumber}", name="ManagementGetUsersWithSortAndPage")
=======
     * @Route("/manage/users/page={pageNumber}", name="ManagementGetUsersWithPage")
     * @Route("/manage/users/sort={sortBy}-{order}", name="ManagementGetUsersWithSort")
     * @Route("/manage/users/sort={sortBy}-{order}/page={pageNumber}", name="ManagementGetUsersWithSortAndPage")
     * @Route("/manage/users/sort={sortBy}-{order}/q={query}/page={pageNumber}", name="ManagementGetUsersWithSortAndSearchAndPage")
>>>>>>> 9811e59bd4e66894f56c5c5d0bb05c529f4f2935
     */
    public function getAction(Request $request, $sortBy = "id", $order = "ASC", $searchQuery = '', $pageNumber = 1){   
        $UsersManager = $this->get('app.UsersManager');
        $SanitizeManager = $this->get('app.SanitizeManager');
        
        $sortBy = $SanitizeManager->getValidSortBy($sortBy);
        $order = $SanitizeManager->getValidOrder($order);
        $query = $SanitizeManager->getValidQuery($query);
        
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
    
    public function deleteAction($obj){
        
    }
    
    public function updateAction($obj, array $options){
        
    }
}
