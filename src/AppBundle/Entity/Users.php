<?php
// src/AppBundle/Entity/Users.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 */
class Users
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $Id;  

    /**
    * @ORM\Column(type="string", length=256)
    */
    private $Username;  
    
    /**
    * @ORM\Column(type="string", length=256)
    */
    private $Email;  
    
    /**
    * @ORM\Column(type="string", length=256)
    */
    private $Salt;  
    
    /**
    * @ORM\Column(type="string", length=256)
    */
    private $Password;
    
    /**
    * @ORM\Column(type="boolean")
    */
    private $IsVerified;  
    
    /**
    * @ORM\Column(type="string", length=256)
    */
    private $VerificationToken;  
    
    /**
    * @ORM\Column(type="integer")
    */
    private $Reputation;  
    
    /**
     * @ORM\ManyToOne(targetEntity="Sites", inversedBy="Users")
     * @ORM\JoinColumn(name="Site", referencedColumnName="Id")
     */
    private $Site;
    
    /**
    * @ORM\Column(type="datetime")
    */
    private $CreationDate;  
}
