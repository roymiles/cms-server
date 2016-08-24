<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\Interfaces;

class ApiUsersController implements iTable
{
    /**
     * This is the documentation description of your method, it will appear
     * on a specific pane. It will read all the text until the first
     * annotation.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     * @Route("/api/users", name="ApiGetUsers")
     * @Route("/api/users/page={pageNumber}", name="ApiGetUsersWithPage")
     * @Route("/api/users/sort={sortBy}", name="ApiGetUsersWithSort")
     * @Route("/api/users/page={pageNumber}/sort={sortBy}", name="ApiGetUsersWithPageAndSort")
     */
    public function getAction(Request $request, $pageNumber = 1, $sortBy = "id", $order = "ASC")
    {
        $UsersManager = $this->get('app.UsersManager');
        
        // If not a valid sort, sort by users id
        if(!$UsersManager->isColumn($sortBy)){
            $sortBy = 'id';
        }        
        
        $Options = []; // No search criteria
        $Filters = ['sortBy' => $sortBy, 'order' => $order, 'limit' => 10, 'offset' => 0]; //Show 10 at a time
        $Users = $UsersManager->get($Options, $Filters);
        return json_encode($Users);
    }
}
