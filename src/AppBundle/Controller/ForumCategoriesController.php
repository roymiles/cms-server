<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

class ForumCategoriesController extends Controller
{
    //
    // Local Controllers
    //    
    
    /**
     * @Route("/forum", name="ForumCategories")
     * @Route("/forum/page={pageNumber}/", name="ForumCategoriesWithPage")
     * @Route("/forum/sort={sortBy}/", name="ForumCategoriesWithSort")
     * @Route("/forum/page={pageNumber}/sort={sortBy}/", name="ForumCategoriesWithPageAndSort")
     */
    public function forumCategoriesAction(Request $request, $pageNumber = 1, $sortBy = "ASC")
    {
        $ForumManager = $this->get('app.ForumManager');
        $Categories = $ForumManager->getCategories(['siteId' => -1, 'pageNumber' => $pageNumer, 'sortBy' => $sortBy]);
        
        if(count($Categories) < 1){
            // No categories found
            return $this->render('default/forum/error/categories.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
                'error' => "No categories found"
            ]); 
        }
    
        return $this->render('default/forum/categories.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'categories' => $Categories
        ]);
    }  
    
    
    //
    // API Controllers
    //
    
    /**
     * @Route("/api={siteToken}/forum", name="ApiForumCategories")
     */
    public function apiForumCategoriesAction(Request $request, $siteToken)
    {
        $ForumManager = $this->get('app.ForumManager');
        $SiteManager = $this->get('app.SiteManager');
        
        if(!$SiteManager->isValidSiteToken($siteToken)){
            // Site token is invalid
            return $this->json(array('error' => 'Invalid site token'));
        } 
        
        $SiteId = $SiteManager->getSiteIdByToken($siteToken);
        $Categories = $ForumManager->getCategories(['siteId' => $siteToken]);
        
        if(count($Categories) < 1){
            // No categories found
            return $this->json(array('error' => 'No categories found'));
        }        
        
        return $this->json($Categories);
    }    
    
    /**
     * @Route("/api={siteToken}/forum/page={pageNumber}/", name="ApiForumCategoriesPage")
     */
    public function apiForumCategoriesPageAction(Request $request, $siteToken, $pageNumber)
    {
        $ForumManager = $this->get('app.ForumManager');
        $SiteManager = $this->get('app.SiteManager');
        
        if(!$SiteManager->isValidSiteToken($siteToken)){
            // Site token is invalid
            return $this->json(array('error' => 'Invalid site token'));
        } 
        
        $SiteId = $SiteManager->getSiteIdByToken($siteToken);
        $Categories = $ForumManager->getCategories(['siteId' => $siteId, 'pageNumber' => $pageNumer]);
        
        if(count($Categories) < 1){
            // No categories found
            return $this->json(array('error' => 'No categories found'));
        }        
        
        return $this->json($Categories);
    }    
    
    /**
     * @Route("/api={siteToken}/forum/sort={sortBy}/", name="ApiForumCategoriesSort")
     */
    public function apiForumCategoriesSortAction(Request $request, $siteToken, $sortBy)
    {
        $ForumManager = $this->get('app.ForumManager');
        $SiteManager = $this->get('app.SiteManager');
        
        if(!$SiteManager->isValidSiteToken($siteToken)){
            // Site token is invalid
            return $this->json(array('error' => 'Invalid site token'));
        } 
        
        $SiteId = $SiteManager->getSiteIdByToken($siteToken);
        $Categories = $ForumManager->getCategories(['siteId' => $siteId, 'sortBy' => $sortBy]);
        
        if(count($Categories) < 1){
            // No categories found
            return $this->json(array('error' => 'No categories found'));
        }        
        
        return $this->json($Categories);
    }
    
    /**
     * @Route("/api={siteToken}/forum/page={pageNumber}/sort={sortBy}/", name="ApiForumCategoriesPageSort")
     */
    public function apiForumCategoriesPageSortAction(Request $request, $siteToken, $pageNumber, $sortBy)
    {
        $ForumManager = $this->get('app.ForumManager');
        $SiteManager = $this->get('app.SiteManager');
        
        if(!$SiteManager->isValidSiteToken($siteToken)){
            // Site token is invalid
            return $this->json(array('error' => 'Invalid site token'));
        } 
        
        $SiteId = $SiteManager->getSiteIdByToken($siteToken);
        $Categories = $ForumManager->getCategories(['siteId' => $siteId, 'pageNumber' => $pageNumber, 'sortBy' => $sortBy]);
        
        if(count($Categories) < 1){
            // No categories found
            return $this->json(array('error' => 'No categories found'));
        }        
        
        return $this->json($Categories);
    }      

 
}
