<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Added namespaces
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use AppBundle\Entity\Documentation;
use AppBundle\Exception\DocumentationNotFound;

use AppBundle\Forms\DocumentationType;

use AppBundle\Exception\NoSiteTokenSupplied;
use AppBundle\Exception\InvalidSiteToken;
use AppBundle\Exception\AuthorisationError;


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
        
        if(!$this->isGranted('GET', $documentation)){
            throw new AuthorisationError(
                'You are not granted to perform this action'
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
     * @Method({"GET"})
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
        
        // Check if user has privileges to edit this documentation
        if(!$this->isGranted('EDIT', $documentation)){
            throw new AuthorisationError(
                'You are not granted to perform this action'
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
            ->setAction($this->generateUrl('ProcessDocumentationEdit', array('documentationId' => $documentationId)))
            ->add('PageContent', TextareaType::class, array('attr' => array('class' => 'page-editor'), 'label' => false))
            ->getForm();
        
        return $this->render('default/docs/pages/edit.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'activeTab' => 'documentation',
            'documentation' => $documentation,
            'breadcrumbs' => $breadcrumbs,
            'form' => $editDocumentationForm->createView()
        ]);
    }    
       
    /**
     * When the form is submitted, this controller will be executed
     * @Route("/documentation/edit/id={documentationId}", name="ProcessDocumentationEdit")
     * @Method({"POST"})
     */
    public function editDocumentationProcessAction(Request $request, $documentationId)
    {
        $documentationManager   = $this->get('app.DocumentationManager');
        $ValidationManager      = $this->get('app.ValidationManager');
        $documentation = $documentationManager->get(['Id' => $documentationId], ['limit' => 1]);

        if(empty($documentation)){
            throw new DocumentationNotFound(
                'Documentation does not exist'
            );    
        }
        
        // Check if user has privileges to edit this documentation
        if(!$this->isGranted('EDIT', $documentation)){
            throw new AuthorisationError(
                'You are not granted to perform this action'
            );
        }
        
        $editedDocumentation = $request->request->get('documentation');
        
        // Check if all field elements exist, if not redirect back to register form
        if(['pageContent', '_token'] != array_keys($editedDocumentation)){
            $this->addFlash('documentationEditErrors', "Malformed request");
            return $this->redirect('ViewDocumentation', array('id' => $documentationId));
        }
        
        // Validate the page content
        $ValidationManager->documentationContent($editedDocumentation['Username']);  
        
        $errors = $ValidationManager->getErrors();
        foreach($errors as $error){
            // addFlash pushes each element into an array
            $this->addFlash('documentationEditErrors', $error);
        }        
        
        // If there are errors in the site_token, redirect immediately
        if(!empty($errors)){
            return $this->redirect('ViewDocumentation', array('id' => $documentationId));
        }
        
        // Passed all validation, so add the user
        $documentationManager->update($documentation, ['pageContent' => 's']);
        
        // Redirect to the documentation page
        
        echo "hi";die;
    }
}
