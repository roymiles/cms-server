<?php
// src/AppBundle/Services/UsersManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Doctrine\ORM\EntityManager;

use AppBundle\Services\Interfaces;
use AppBundle\Services\SitesManager;

class UsersManager
{
    private $repository;
    public function __construct(EntityManager $em, RecursiveValidator $validator, SitesManager $SitesManager)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->SitesManager = $SitesManager;
        
        $this->repository = 'AppBundle\Entity\Users';
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
                         ->findBy($options, array($filters['sortBy'] => $filters['order']), $filters['limit'], $filters['offset']);
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
    private $Errors;
    public function getErrors(){
        $Errors = $this->Errors;
        // Errors are cleared once read
        $this->Errors = array();
        return $Errors;
    }
    
    private function addError($Error){
        $this->Errors[] = $Error;
    }
    
    // Not being used
    private $EntityConstraintErrors;
    public function getEntityConstraintErrors(){
        $EntityConstraintErrors = $this->EntityConstraintErrors;
        // Errors are cleared once read
        $this->EntityConstraintErrors = array();
        return $EntityConstraintErrors;
    }
    
    public function add(array $Options)
    {
        $User = new Users();
        
        $Username = $Options['Username'];
        $Email    = $Options['Email'];
        $Password = $Options['Password'];
        
        $User->setUsername($Username);
        $User->setEmail($Email);
        $User->setPassword($Password);
        
        // Get the site
        $SiteId = $Options['SiteId'];
        $Site = $this->SitesManager->get(['Id' => $SiteId], ['limit' => 1]);
        $numErrors = 0;

        // Check if a user doesnt already exist for the given username or email
        if(!$this->isUnique($Username, $Email, $Site)){
            $this->addError("User already exists for the current site");
            $numErrors++;
        }
        
        // Check if password is valid
        if(!$this->isValidPassword($Password)){
            $numErrors++;
        }
        
        // Validate with entity annotation constraints
        // Temporary removal as cant seem to print friendly error messages
        /*$errors = $this->validator->validate($User);    
        if(count($errors) > 0){
            $this->EntityConstraintErrors = $errors;
            return false;
        }*/
        
        // Check if username is valid
        if(!$this->isValidUsername($Username)){
            $numErrors++;
        }
         
        // Check if email is valid
        if(!$this->isValidEmail($Email)){
            $numErrors++;
        }
        
        // Username should not be the same as the password
        if($Username == $Password){
            $this->addError('Username can\'t be the same as the password');
            $numErrors++;   
        }
        
        // Email should not be the same as the password
        if($Email == $Password){
            $this->addError('Email can\'t be the same as the password');
            $numErrors++;   
        }   
        
        if($numErrors == 0){
            // Passed validation
            $this->em = $this->getDoctrine()->getManager();

            // Tells Doctrine you want to (eventually) save the User (no queries yet)
            $this->em->persist($User);

            // Actually executes the queries (i.e. the INSERT query)
            $this->em->flush();   
            
            return true;
        }else{
            // There has been some errors
            return false;
        }
        
    }    
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------  
    
    public function isValidPassword(string $Password){
        $numErrors = 0;
        if (strlen($Password) <= 8) {
            $this->addError("Password must contain at least 8 characters");
            $numErrors++;
        }
        
        if (strlen($Password) > 100) {
            $this->addError("Password can't be longer than 100 characters");
            $numErrors++;
        }
        
        if(!preg_match("#[0-9]+#", $Password)) {
            $this->addError("Password must contain at least 1 number");
            $numErrors++;
        }
        
        if(!preg_match("#[A-Z]+#", $Password)) {
            $this->addError("Password must contain at least 1 capital letter");
            $numErrors++;
        }
        
        if(!preg_match("#[a-z]+#", $Password)) {
            $this->addError("Password must contain at least 1 lowercase letter");
            $numErrors++;
        }
        
        if($numErrors == 0){
            // No errors
            return true;
        }else{
            return false;
        }
    }
    
    public function isValidUsername($Username){
        $numErrors = 0;
        if (strlen($Username) <= 4) {
            $this->addError("Username must contain at least 4 characters");
            $numErrors++;
        }
        
        if (strlen($Username) > 100) {
            $this->addError("Username can't be longer than 100 characters");
            $numErrors++;
        }
        
        if($numErrors == 0){
            // No errors
            return true;
        }else{
            return false;
        }
    }
    
    public function isValidEmail($Email){
        $numErrors = 0;
        if (strlen($Email) <= 4) {
            $this->addError("Email must contain at least 4 characters");
            $numErrors++;
        }
        
        if (strlen($Email) > 100) {
            $this->addError("Email can't be longer than 100 characters");
            $numErrors++;
        }
        
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $this->addError("Invalid email address");
            $numErrors++;
        }
        
        if($numErrors == 0){
            // No errors
            return true;
        }else{
            return false;
        } 
    }
    
    public function isUnique(string $Username, string $Email, $Site){
        $query = $this->em->createQueryBuilder()
                ->select('u')
                ->from('AppBundle\Entity\Users', 'u')
                ->innerJoin('AppBundle\Entity\Sites', 's', 'WITH', 's.Id = u.Site')
                ->where('s.Id = :SiteId AND (u.Username = :Username OR u.Email = :Email)')
                ->setParameters(['SiteId' => $Site->getId(),
                                 'Username' => $Username,
                                 'Email' => $Email])
                ->getQuery();
        
        $User = $query->getOneOrNullResult();
        if($User){
            return false; // User already exists
        }else{
            return true; // No user currently exists
        }
    }
    
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
        if($user1->getId() == $user2->getId()){
            return true;
        }else{
            return false;
        }
    }
    
    public function serialize($user){
        // Some of these fields may be sensitive, perhaps remove them
        return array(
            "Id" => $user->getId(),
            "Username" => $user->getUsername(),
            "Email" => $user->getEmail(),
            "Password" => $user->getPassword(),
            "IsVerified" => $user->getIsVerified(),
            "VerificationToken" => $user->getVerificationToken(),
            "Reputation" => $user->getReputation(),
            "Site" => $user->getSite(),
            "Roles" => $user->getRoles(),
            "CreationDate" => $user->getCreationDate()
        );
    }

    
}
