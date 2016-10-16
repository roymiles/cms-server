<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Documentation;
use AppBundle\Exception\DocumentationNotFound;

class DocumentationController extends Controller
{   
    /**
     * @Route("/documentation", name="DocumentationHome", defaults={"documentationId" = 1})
     * @Route("/documentation/id={documentationId}", name="ViewDocumentation")
     */
    public function viewDocumentationAction(Request $request, $documentationId)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Id' => $documentationId], ['limit' => 1]);
        
        if(empty($documentation)){
            throw new DocumentationNotFound(
                'Documentation does not exist'
            );    
        }
        
        $breadcrumbs = array();
        // Push the current documentation into the array
        array_push($breadcrumbs, array('enabled' => true, 'path' => 'ViewDocumentation', 'parameters' => ['documentationId' => $documentation->getId()], 'name' => $documentation->getName()));
        $parentDocumentation = $documentation->getParentDocumentation();
        // Push all proceeding parent documentations into the array
        while($parentDocumentation != null){
            array_push($breadcrumbs, array('enabled' => true, 'path' => 'ViewDocumentation', 'parameters' => ['documentationId' => $parentDocumentation->getId()], 'name' => $parentDocumentation->getName()));  
            $parentDocumentation = $parentDocumentation->getParentDocumentation();
        }
        // Flip the array around so that the root parents appear first
        $breadcrumbs = array_reverse($breadcrumbs);
        
        $documentationTree = $documentationManager->get([], ['limit' => 999]); // Get all the documentation pages
        $treeManager = $this->get('app.TreeManager');
        $quicklinks = $treeManager->makeTree($documentationTree, 'ParentDocumentation');
        
        return $this->render('default/docs/pages/view.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'documentation',
            'documentation' => $documentation,
            'breadcrumbs' => $breadcrumbs,
            'quicklinks' => $quicklinks
        ]);
        
    }
    
    /**
     * @Route("/documentation/edit/id={documentationId}", name="EditDocumentation")
     */
    public function editDocumentationAction(Request $request, $documentationId)
    {
        $documentationManager = $this->get('app.DocumentationManager');
        $documentation = $documentationManager->get(['Id' => $documentationId], ['limit' => 1]);

        if(empty($documentation)){
            throw new DocumentationNotFound(
                'Documentation does not exist'
            );    
        }
        
        $breadcrumbs = array();
        // Push the current documentation into the array
        array_push($breadcrumbs, array('enabled' => true, 'path' => 'ViewDocumentation', 'parameters' => ['documentationId' => $documentation->getId()], 'name' => $documentation->getName()));
        $parentDocumentation = $documentation->getParentDocumentation();
        // Push all proceeding parent documentations into the array
        while($parentDocumentation != null){
            array_push($breadcrumbs, array('enabled' => true, 'path' => 'ViewDocumentation', 'parameters' => ['documentationId' => $parentDocumentation->getId()], 'name' => $parentDocumentation->getName()));  
            $parentDocumentation = $parentDocumentation->getParentDocumentation();
        }
        // Flip the array around so that the root parents appear first
        $breadcrumbs = array_reverse($breadcrumbs);
        
        // Form builder
        $d = new Documentation();
        $d->setPageContent($documentation->getPageContent());

        $editDocumentationForm = $this->createFormBuilder($d)
            ->add('PageContent', TextareaType::class, array('attr' => array('class' => 'page-editor'), 'label' => false))
            ->add('Save', SubmitType::class, array('attr' => array('style' => 'float:right'), 'label' => 'Save Documentation'))
            ->getForm();
        
        return $this->render('default/docs/pages/edit.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'documentation',
            'documentation' => $documentation,
            'breadcrumbs' => $breadcrumbs,
            'form' => $editDocumentationForm->createView()
        ]);
    }    
          
}
