<?php
// src/AppBundle/Services/UserRelationshipsManager.php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

use AppBundle\Services\Interfaces\iTable;

class UserRelationshipsManager implements iTable
{
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle:Entity:UserRelationships';
    }
    
    //-----------------------------------------------------
    // SELECT actions
    //-----------------------------------------------------    
    
    //-----------------------------------------------------
    // DELETE actions
    //-----------------------------------------------------        
    
    public function delete($Relationship)
    {
        $this->em->remove($Relationship);
        $this->em->flush();
    }
    
    //-----------------------------------------------------
    // UPDATE actions
    //----------------------------------------------------- 
    
    public function update($Relationship, array $options)
    {
        foreach($options as $column => $newValue){
          switch($column){
            case 'RelatingUser':
              $Relationship->setRelatingUser($newValue);
              break;
          }
        }
        
        $em->flush();      
    }
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------       
    public function add(array $options)
    {
        
    }    
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------   
 
}
