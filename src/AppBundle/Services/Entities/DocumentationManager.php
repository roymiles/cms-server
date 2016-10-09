<?php
// src/AppBundle/Services/Entities/DocumentationManager.php

namespace AppBundle\Services\Entities;

use AppBundle\Entity\Documentation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class DocumentationManager
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\Documentation';
    }
    
    public function get(array $options, array $filters){
        // Set default values
        if(!isset($filters['sortBy'])){
            $filters['sortBy'] = 'Id';
        }

        if(!isset($filters['order'])){
            $filters['order'] = 'ASC';
        }
        
        if(!isset($filters['limit'])){
            $filters['limit'] = 1;
        }
    
        if(!isset($filters['offset'])){
            $filters['offset'] = 0;
        }
        
        if($filters['limit'] == 1){
            // Expecting a single result
            $doc = $this->em
                         ->getRepository($this->repository)
                         ->findOneBy($options);
        }else{  
            $doc = $this->em
                         ->getRepository($this->repository)
                         ->findBy($options, array($filters['sortBy'] => $filters['order']), $filters['limit'], $filters['offset']);
        }
        
        return $doc; 
    }
}
