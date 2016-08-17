<?php
// src/AppBundle/Services/ForumManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Sites;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class ForumCategoriesManager
{
    
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle:Entity:ForumCategories';
    }
    
    private $flashError;
    public function getFlashError(){
      $fe = $this->flashError;
      unset($this->$flashError);
      return $fe;
    }
    
    //-----------------------------------------------------
    // SELECT actions
    //-----------------------------------------------------      
    
    public function getCategories(Array $Options){
      // Bleh     
    }
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------      
    
    //-----------------------------------------------------
    // DELETE actions
    //-----------------------------------------------------      
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------      
    
}
