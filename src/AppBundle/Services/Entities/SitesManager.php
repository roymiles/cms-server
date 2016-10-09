<?php

// src/AppBundle/Services/Entities/SitesManager.php

namespace AppBundle\Services\Entities;

use AppBundle\Entity\Sites;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use AppBundle\Services\Interfaces\iTable;

class SitesManager implements iTable
{
    const $LocalSiteToken = 'a'; // So other services/controllers can access the local site token
    
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
    
    public function count(array $options = null){
        // See: http://stackoverflow.com/questions/9214471/count-rows-in-doctrine-querybuilder
        return $this->em
                    ->getRepository($this->repository)
                    ->createQueryBuilder('s')
                    ->select('count(s.Id)')
                    ->getQuery()
                    ->getSingleScalarResult();
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
        $Site = new Sites();
        
        $Owner = $Options['Owner'];
        if(!$Owner instanceof Users){
            throw new \Exception('Owner is not an instance of Users');
        }
        
        $DomainName = $Options['DomainName'];
        
        $Site->setOwner($Owner);
        $Site->setDomainName($DomainName);
        
        $token = md5(random_bytes(10));
        $Site->setToken($token);

        // Tells Doctrine you want to (eventually) save the User (no queries yet)
        $this->em->persist($Site);

        // Actually executes the queries (i.e. the INSERT query)
        $this->em->flush();   

        return $Site;     
    }    
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------       
    
    
    public function isEqual($site1, $site2){
        if(!$site1 instanceof Sites || !$site2 instanceof Sites){
            return false;
        }
        
        if($site1->getId() == $site2->getId()){
            return true;
        }else{
            return false;
        }
    }    
    
}
