<?php
// src/AppBundle/Entity/LoginAttempts.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="LoginAttempts")
 */
class LoginAttempts
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="LoginAttempts")
     * @ORM\JoinColumn(name="User", referencedColumnName="Id")
     */
    private $User;    
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $Time;
}
