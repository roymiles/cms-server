<?php
// src/AppBundle/Services/ValidationManager.php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

// Entities
use AppBundle\Entity\Users;
use AppBundle\Entity\Sites;

class ValidationManager
{
    private $isErrors           = false;
    private $validationErrors   = array();
     
    public function __construct(Container $container){
        $this->container = $container;
        
        $this->validationErrors = array();
        $this->isErrors         = false;
    }
    
    private function addError($message){
        array_push($this->validationErrors, $message);
        $this->isErrors = true;
    }
    
    public function getErrors(){
        $errors = $this->validationErrors;
        // Empty the error array once read
        $this->validationErrors = array();
        $this->isErrors = false;
        return $errors;
    }
    
    // Validate the site_token
    public function site_token($site_token){
        if(!$site_token){
            $this->addError('No Site Token Supplied');
            return false;
        } 
        
        // Does the token correspond to a valid site
        $sitesManager = $this->container->get('app.SitesManager');
        $Site = $sitesManager->get(['Token' => $site_token], ['limit' => 1]);
        if(!$Site){
            $this->addError('Invalid Site Token');
            return false;
        }else{
            return $Site;
        }
    }
    
    // If isUnique is set to true, the function will also validate the uniqueness
    // of the email within the current $site
    public function email($email, $isUnique = false, $site = null){
        if (strlen($email) <= 4) {
            $this->addError("Email must contain at least 4 characters");
        }
        
        if (strlen($email) > 100) {
            $this->addError("Email can't be longer than 100 characters");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError("Invalid email address");
        }
        
        if($isUnique && $site instanceof Site){
            // Check if the email is unique within the site
            $usersManager = $this->container->get('app.UsersManager');
            $user = $usersManager->get(['Email' => $email, 'Site' => $site], ['limit' => 1]);
            if($user instanceof Users){
                $this->addError('Email is already taken');
            }    
        }
        
        // If there are validation errors, return false
        return $this->isErrors ? true : false;   
    }
    
    public function username($username, $isUnique = false, $site = null){
        if (strlen($username) <= 4) {
            $this->addError("Username must contain at least 4 characters");
        }
        
        if (strlen($username) > 100) {
            $this->addError("Username can't be longer than 100 characters");
        }
        
        if($isUnique && $site instanceof Site){
            // Check if the username is unique within the site
            $usersManager = $this->container->get('app.UsersManager');
            $user = $usersManager->get(['Username' => $username, 'Site' => $site], ['limit' => 1]);
            if($user instanceof Users){
                $this->addError('Username is already taken');
            } 
        }
        
        // If there are validation errors, return false
        return $this->isErrors ? true : false;        
    }
    
    // If $comparisons is supplied, the function will check if they are both equal
    public function password($password, $fieldsNotEqualToPassword = array(), $repeatPassword = null){
        if (strlen($password) <= 8) {
            $this->addError("Password must contain at least 8 characters");
        }
        
        if (strlen($password) > 100) {
            $this->addError("Password can't be longer than 100 characters");
        }
        
        if(!preg_match("#[0-9]+#", $password)) {
            $this->addError("Password must contain at least 1 number");
        }
        
        if(!preg_match("#[A-Z]+#", $password)) {
            $this->addError("Password must contain at least 1 capital letter");
        }
        
        if(!preg_match("#[a-z]+#", $password)) {
            $this->addError("Password must contain at least 1 lowercase letter");
        }
        
        if(array_key_exists('username', $fieldsNotEqualToPassword)){
            if($fieldsNotEqualToPassword['username'] == $password){
                $this->addError("Password should not be the same as the username");
            } 
        }
        
        if(array_key_exists('email', $fieldsNotEqualToPassword)){
            if($fieldsNotEqualToPassword['email'] == $password){
                $this->addError("Password should not be the same as the email");
            } 
        }

        if($repeatPassword){
            if($repeatPassword != $password){
                $this->addError("Passwords are not the same");     
            }
        }
        
        // If there are validation errors, return false
        return $this->isErrors ? true : false;
    }
    
}
