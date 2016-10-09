<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Exception\ModuleNotFound;
use AppBundle\Exception\DocumentationNotFound;

class DocumentationController extends Controller
{   
    private $breadcrumbs = array();
    private $quicklinks = array();
    // Refactored function to avoid duplicate code in rendering the same twig files
    // - Checks if documentation exists and renders the appropriate twig template
    private function renderDocumentation($documentation)
    {
        if(!empty($documentation))
        {
            return $this->render('default/docs/pages/view.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'activeTab' => 'docs',
                'documentation' => $documentation,
                'breadcrumbs' => $this->breadcrumbs,
                'quicklinks' => $this->quicklinks
            ]);
            
        }else{
            // Module does not exist
            throw new ModuleNotFound(
                'Documentation does not exist'
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
        
        $this->quicklinks = array();
        $documentationTree = $documentationManager->get([], ['limit' => 999]); // Get all the documentations
        $treeManager = $this->get('app.TreeManager');
        $this->quicklinks = $treeManager->makeTree($documentationTree, 'ParentDoc');

        $this->breadcrumbs = array();
        array_push($this->breadcrumbs, array('active' => true, 'path' => 'DocumentationHome', 'name' => 'Home'));          
        
        return $this->renderDocumentation($documentation);
    }
    
    /**
     * @Route("/documentation/modules", name="DocumentationModuleHome")
     */
    public function docsModuleAction(Request $request)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Name' => 'modules'], ['limit' => 1]);
       
        $this->breadcrumbs = array();
        array_push($this->breadcrumbs, array('active' => false, 'path' => 'DocumentationHome', 'name' => 'Home'));   
        array_push($this->breadcrumbs, array('active' => true, 'path' => 'DocumentationModuleHome', 'name' => 'Modules'));   
        
        return $this->renderDocumentation($documentation);
    }    
    
    /**
     * @Route("/documentation/modules/{moduleName}", name="DocumentationModulePage")
     */
    public function docsModulePageAction(Request $request, $moduleName)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Name' => $moduleName], ['limit' => 1]);

        $this->breadcrumbs = array();
        array_push($this->breadcrumbs, array('active' => false, 'path' => 'DocumentationHome', 'name' => 'Home')); 
        array_push($this->breadcrumbs, array('active' => false, 'path' => 'DocumentationModuleHome', 'name' => 'Modules'));   
        array_push($this->breadcrumbs, array('active' => true, 'path' => 'DocumentationModulePage', 'parameters' => ['moduleName' => $moduleName], 'name' => $moduleName)); 
        
        return $this->renderDocumentation($documentation);
    }    
    
    /**
     * @Route("/documentation/edit/id={docId}", name="EditDocumentation")
     */
    public function editDocsAction(Request $request, $docId)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Id' => $docId], ['limit' => 1]);

        if(empty($documentation)){
            throw new DocumentationNotFound(
                'Documentation does not exist'
            );    
        }
        
        $this->breadcrumbs = array();
        array_push($this->breadcrumbs, array('active' => false, 'path' => 'DocumentationHome', 'name' => 'Home')); 
        array_push($this->breadcrumbs, array('active' => true, 'path' => '', 'name' => '...'));   
        array_push($this->breadcrumbs, array('active' => true, 'path' => '', 'name' => 'edit'));   
        array_push($this->breadcrumbs, array('active' => true, 'path' => 'EditDocumentation', 'parameters' => ['docId' => $docId],'name' => $documentation->getName())); 
        
        return $this->render('default/docs/pages/edit.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'docs',
            'documentation' => $documentation,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }    
          
}
