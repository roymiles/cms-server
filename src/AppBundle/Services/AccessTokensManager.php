<?php
// src/AppBundle/Services/AccessTokensManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Documentation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class AccessTokensManager
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\AccessTokens';
    }
    
    
    //-----------------------------------------------------
    // SELECT actions
    //-----------------------------------------------------    
    
    public function get(array $options, array $filters)
    {
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
            $accessToken = $this->em
                         ->getRepository($this->repository)
                         ->findOneBy($options);
        }else{  
            $accessToken = $this->em
                         ->getRepository($this->repository)
                         ->findBy($options, array($filters['sortBy'] => $filters['order']), $filters['limit'], $filters['offset']);
        }
        
        return $accessToken; 
    }
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------      
    public function add($user)
    {
        $accessToken = new AccessTokens();
        $token = md5(random_bytes(10));
        
        $accessToken->setToken($token);
        $accessToken->setUser($user);
        
        // Tells Doctrine you want to (eventually) save the User (no queries yet)
        $this->em->persist($accessToken);

        // Actually executes the queries (i.e. the INSERT query)
        $this->em->flush();
        
        return $token;       
    }        
}
