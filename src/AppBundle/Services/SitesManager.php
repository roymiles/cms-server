<?php
// src/AppBundle/Services/SitesManager.php
namespace AppBundle\Services;

use AppBundle\Entity\Sites;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManager;

class SitesManager
{
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\Sites';
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
            $site = $this->em
                         ->getRepository($this->repository)
                         ->findOneBy($options);
        }else{
            $site = $this->em
                         ->getRepository($this->repository)
                         ->findBy($options, array($filters['sortBy'] => $filters['order']), $filters['limit'], $filters['offset']);
        }
    
        return $site; 
    }
    
    //-----------------------------------------------------
    // DELETE actions
    //-----------------------------------------------------        
    
    public function delete($Site)
    {
        $this->em->remove($Site);
        $this->em->flush();
    }
    
    //-----------------------------------------------------
    // UPDATE actions
    //----------------------------------------------------- 
    
    public function update($Site, array $Options)
    {    
    }
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------       
    public function add(array $Options)
    {      
        
    }    
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------       
    
}
