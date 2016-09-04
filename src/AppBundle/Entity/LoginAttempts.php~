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

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return LoginAttempts
     */
    public function setTime($time)
    {
        $this->Time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->Time;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return LoginAttempts
     */
    public function setUser(\AppBundle\Entity\Users $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->User;
    }
}
