<?php
// src/AppBundle/Services/UsersManager.php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class UsersManager
{
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\Users';
    }
    
    //-----------------------------------------------------
    // SELECT actions
    //-----------------------------------------------------    
    
    public function get(array $options, array $filters)
    {
        $user = $this->em
                     ->getRepository($this->repository)
                     ->findBy($options, ['username' => $filters['orderBy']], $filters['limit'], $filters['offset']);
    
        if (!$user) {
            return false;
        }
    
        return $user; 
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
 
    public function isValidEmail(string $Email, int $SiteId){}
    public function isValidUsername(string $Username, int $SiteId){}
    public function isValidPassword(string $Password){}
    public function isUniqueUsername(string $Username, $SiteId){}
    public function isUniqueEmail(string $Email, $SiteId){}
    
    public function verifyCredentials(string $UsernameOrEmail, string $Password, int $SiteId)
    {
        // Check username first
        $User = $this->getUserByUsername($UsernameOrEmail, $SiteId);
        if(!$UsernameOrEmail){
            // Username doesnt exist
            $User = $this->getUserByEmail($UsernameOrEmail, $SiteId);
            if(!$User){
                // Email doesnt exist
                die('Cant find');
            }
        }
        
        if(password_verify($Password, $User->password)){
            return true;
        }else{
            return false;
        }
    }

    
}
