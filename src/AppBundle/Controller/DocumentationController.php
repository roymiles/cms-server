<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DocumentationController extends Controller
{   
    // Refactored function to avoid duplicate code in rendering the same twig files
    // * Checks if documentation exists and renders the appropriate twig template
    private function renderDocumentation($documentation)
    {
        if(!empty($documentation))
        {
            return $this->render('default/docs/markup/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'activeTab' => 'docs',
                'documentation' => $documentation
            ]);
            
        }else{
            // Module does not exist
            return $this->render('default/docs/404.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'activeTab' => 'docs',
            ]);       
        }    
    }

    /**
     * @Route("/documentation", name="DocumentationHome")
     */
    public function docsAction(Request $request)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Name' => 'home']);
        return $this->renderDocumentation($documentation);
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
        $documentation = $documentationManager->get(['name' => $moduleName, 'parentDoc.name' => 'modules']);
        return $this->renderDocumentation($documentation);
    }    
    
}
