<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Route("/search/{query}", name="search")
     */
    public function searchAction(Request $request, $query)
    {
        $searchManager = $this->get('app.SearchManager');
        $results = $searchManager->search(['limit' => 10]);
        
        if(!empty($search)){
          return $this->render('default/search/index.html.twig', [
              'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
              'results' => $results
          ]);
        }else{
          return $this->render('default/search/404.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..')
          ]);
        }
    }
    
}
