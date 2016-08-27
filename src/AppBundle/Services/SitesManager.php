<?php
// src/AppBundle/Services/Api/SitesManager.php
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
        
        $site = $this->em
                     ->getRepository($this->repository)
                     ->findBy($options, array($filters['sortBy'] => $filters['order']), $filters['limit'], $filters['offset']);
    
        return $site; 
    }
    
    //-----------------------------------------------------
    // DELETE actions
    //-----------------------------------------------------        
    
    public function delete($User)
    {
        $this->em->remove($User);
        $this->em->flush();
    }
    
    //-----------------------------------------------------
    // UPDATE actions
    //----------------------------------------------------- 
    
    public function update($User, array $Options)
    {
        $User->setUsername($Username);
        $em->flush();      
    }
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------       
    public function add(string $username, string $email, string $password)
    {
        $user = new Users();
        
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword(password_hash($password));
        
        $this->em = $this->getDoctrine()->getManager();
    
        // Tells Doctrine you want to (eventually) save the User (no queries yet)
        $this->em->persist($user);
    
        // Actually executes the queries (i.e. the INSERT query)
        $this->em->flush();        
        
    }    
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------       
    
}
