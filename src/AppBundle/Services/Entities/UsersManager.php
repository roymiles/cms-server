<?php

// src/AppBundle/Services/Entities/UsersManager.php

namespace AppBundle\Services\Entities;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Doctrine\ORM\EntityManager;

// Interfaces
use AppBundle\Services\Interfaces\iTable;

// Entities
use AppBundle\Entity\Users;

// Services
use AppBundle\Services\Entities\SiteManager;
use AppBundle\Services\Entities\ValidationManager;

class UsersManager implements iTable
{
    private $repository;
    public function __construct(EntityManager $em, RecursiveValidator $validator, $sitesManager, $validationManager)
    {
        $this->em                = $em;
        $this->validator         = $validator;
        $this->sitesManager      = $sitesManager;
        $this->validationManager = $validationManager;
        
        $this->repository    = 'AppBundle\Entity\Users';
        $this->errorMessages = array();
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
            $user = $this->em
                         ->getRepository($this->repository)
                         ->findOneBy($options);
        }else{  
            $user = $this->em
                         ->getRepository($this->repository)
                         //->createQueryBuilder('cat')
                         //->andWhere('cat.name LIKE :searchTerm')
                         //->setParameter('searchTerm', '%ad%')

                         ->findBy($options, array($filters['sortBy'] => $filters['order']), $filters['limit'], $filters['offset']);
        }
        
        return $user; 
    }
    
    public function count(array $options){
        if(!array_key_exists('SiteId', $options)){
            return false;
        }
        // See: http://stackoverflow.com/questions/9214471/count-rows-in-doctrine-querybuilder
        return $this->em
                    ->getRepository($this->repository)
                    ->createQueryBuilder('u')
                    ->select('count(u.Id)')
                    ->where('u.Site = :SiteId')
                    ->setParameter('SiteId', $options['SiteId'])
                    ->getQuery()
                    ->getSingleScalarResult();
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
        // Needs to be implemented
        $User->setUsername($Username);
        $em->flush();      
    }
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------
    
    // Not being used
    /*private $EntityConstraintErrors;
    public function getEntityConstraintErrors(){
        $EntityConstraintErrors = $this->EntityConstraintErrors;
        // Errors are cleared once read
        $this->EntityConstraintErrors = array();
        return $EntityConstraintErrors;
    }*/
    
    public function add(array $Options)
    {
        $User = new Users();
        
        $Username = $Options['Username'];
        $Email    = $Options['Email'];
        $Password = $Options['Password'];
        
        $User->setUsername($Username);
        $User->setEmail($Email);
        $User->setPassword($Password);
        
        // Unless specified, the new user will not be verified automatically
        $isVerified = isset($Options['isVerified']) ? $Options['isVerified'] : 0;
        $User->setIsVerified($isVerified);
        
        // The verification token will be a hash of a securely generated sequence of 10 bytes
        $verificationToken = md5(random_bytes(10));
        $User->setVerificationToken($verificationToken);
        
        // Unless specified, the new user will start with 1 reputation
        $Reputation = isset($Options['Reputation']) ? $Options['Reputation'] : 1;
        $User->setReputation($Reputation);    
        
        // Get the site
        $Site = $Options['Site'];
        $User->setSite($Site);
        
        // Tells Doctrine you want to (eventually) save the User (no queries yet)
        $this->em->persist($User);

        // Actually executes the queries (i.e. the INSERT query)
        $this->em->flush();   
        
    }    
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------  
    
    public function verifyCredentials(string $UsernameOrEmail, string $Password, $Site)
    {   
        $query = $this->em->createQueryBuilder()
                ->select('u')
                ->from('AppBundle\Entity\Users', 'u')
                ->innerJoin('AppBundle\Entity\Sites', 's', 'WITH', 's.Id = u.Site')
                ->where('s.Id = :SiteId AND (u.Username = :Username OR u.Email = :Email)')
                ->setParameters(['SiteId' => $Site->getId(),
                                 'Username' => $UsernameOrEmail,
                                 'Email' => $UsernameOrEmail])
                ->getQuery();
        
        $User = $query->getOneOrNullResult();
        if($User){
            if(password_verify($Password, $User->getPassword())){
                return $User;
            }else{
                $this->addError("Invalid password");
                return false;
            }
        }else{
            $this->addError("Email or Username does not correspond to a user");
            return false;
        }
    }
    
    public function isEqual($user1, $user2){
        if(!$user1 instanceof Users || !$user2 instanceof Users){
            return false;
        }
        
        if($user1->getId() == $user2->getId()){
            return true;
        }else{
            return false;
        }
    }
    
    public function serialize($user, $removeSensitiveFields = false){
        $u = array(
            "Id"                => $user->getId(),
            "Username"          => $user->getUsername(),
            "Email"             => $user->getEmail(),
            "Password"          => $user->getPassword(),
            "IsVerified"        => $user->getIsVerified(),
            "VerificationToken" => $user->getVerificationToken(),
            "Reputation"        => $user->getReputation(),
            "Site"              => $user->getSite(),
            "Roles"             => $user->getRoles(),
            "CreationDate"      => $user->getCreationDate()
        );
        
        if($removeSensitiveFields){
            unset($u['Password']);
            unset($u['VerificationToken']);
        }
        
        return $u;
    }

    
}
