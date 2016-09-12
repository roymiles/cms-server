<?php
// src/AppBundle/Entity/AccessTokens.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="AccessTokens")
 */
class AccessTokens
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     */
    private $Token;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="AccessTokens")
     * @ORM\JoinColumn(name="UserId", referencedColumnName="Id")
     * @Assert\NotBlank()
     */
    private $User;


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
     * Set token
     *
     * @param string $token
     *
     * @return AccessTokens
     */
    public function setToken($token)
    {
        $this->Token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->Token;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return AccessTokens
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
