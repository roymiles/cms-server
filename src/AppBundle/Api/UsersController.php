<?php

namespace AppBundle\Api\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class UsersController extends FOSRestController
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
    public function getUsersAction()
    {
        $data = ...; // get data, in this case list of users.
        $view = $this->view($data, 200)
            ->setTemplate("MyBundle:Users:getUsers.html.twig")
            ->setTemplateVar('users')
        ;

        return $this->handleView($view);
    }

    public function redirectAction()
    {
        $view = $this->redirectView($this->generateUrl('some_route'), 301);
        // or
        $view = $this->routeRedirectView('some_route', array(), 301);

        return $this->handleView($view);
    }
}
