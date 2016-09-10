<?php
// src/AppBundle/Security/AnonymousUser.php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class AnonymousUser implements UserInterface
{
    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return "Anonymous";
    }
    
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        // Password is encoded with bcrypt, so no salt
        return null;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return null;
    }

    public function getRoles(){
        return array('IS_AUTHENTICATED_ANONYMOUSLY');
    }

    public function eraseCredentials()
    {
    }
}
