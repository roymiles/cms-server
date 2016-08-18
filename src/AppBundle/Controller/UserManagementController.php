<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\Interfaces\iTable;

class UserManagementController extends Controller implements iTable
{  
    /**
     * @Route("/manage/users", name="ManagementGetUsers")
     * @Route("/manage/users/page={pageNumber}", name="ManagementGetUsersWithPage")
     * @Route("/manage/users/sort={sortBy}", name="ManagementGetUsersWithSort")
     * @Route("/manage/users/page={pageNumber}/sort={sortBy}", name="ManagementGetUsersWithPageAndSort")
     */
    public function getAction(Request $request, $pageNumber = 1, $sortBy = "ASC"){
        $UsersManager = $this->get('app.UsersManager');
        $Options = ['SiteId' => 0];
        $Filters = ['orderBy' => $sortBy, 'limit' => 10, 'offset' => 0];
        $Users = $UsersManager->get($Options, $Filters);
        //echo "Sort By: " . $sortBy . " Page Number: " . $pageNumber;
        return $this->render('default/manage/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'users' => $Users
        ]);
    }
    
    public function addAction(array $item){
        
    }
    
    public function deleteAction($obj){
        
    }
    
    public function updateAction($obj, array $options){
        
    }
}
