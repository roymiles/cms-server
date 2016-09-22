<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="Search")
     */
    public function searchAction(Request $request)
    {
        $searchManager = $this->get('app.SearchManager');
        $query = $request->query->get('q');
        $results = $searchManager->search($query, ['Users'], 10);
        
        return $this->render('default/search.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'results' => $results
        ]);
    }
    
}
