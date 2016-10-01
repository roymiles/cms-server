<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Exception\ModuleNotFound;

class DocumentationController extends Controller
{   
    // Refactored function to avoid duplicate code in rendering the same twig files
    // - Checks if documentation exists and renders the appropriate twig template
    private function renderDocumentation($documentation, $breadcrumbs)
    {
        if(!empty($documentation))
        {
            return $this->render('default/docs/markup/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'activeTab' => 'docs',
                'documentation' => $documentation,
                'breadcrumbs' => $breadcrumbs
            ]);
            
        }else{
            // Module does not exist
            throw new ModuleNotFound(
                'No site token supplied'
            );    
        }    
    }

    /**
     * @Route("/documentation", name="DocumentationHome")
     */
    public function docsAction(Request $request)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Name' => 'home'], ['limit' => 1]);
        
        $breadcrumbs = array();
        array_push($breadcrumbs, array('active' => true, 'path' => 'DocumentationHome', 'name' => 'Home'));          
        
        return $this->renderDocumentation($documentation, $breadcrumbs);
    }
    
    /**
     * @Route("/documentation/modules", name="DocumentationModuleHome")
     */
    public function docsModuleAction(Request $request)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['name' => 'modules']);
        return $this->renderDocumentation($documentation);
    }    
    
    /**
     * @Route("/documentation/modules/{moduleName}", name="DocumentationModulePage")
     */
    public function docsModulePageAction(Request $request, $moduleName)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Name' => $moduleName], ['limit' => 1]);

        $breadcrumbs = array();
        array_push($breadcrumbs, array('active' => false, 'path' => 'DocumentationHome', 'name' => 'Home')); 
        array_push($breadcrumbs, array('active' => true, 'path' => 'DocumentationHome', 'name' => $moduleName)); 
        
        return $this->renderDocumentation($documentation, $breadcrumbs);
    }    
          
}
