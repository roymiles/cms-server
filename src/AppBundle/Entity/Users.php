<?php
// src/AppBundle/Entity/Users.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 */
class Users implements UserInterface, \Serializable
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
     * Set username
     *
     * @param string $username
     *
     * @return Users
     */
    public function setUsername($username)
    {
        $this->Username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->Email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->Salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->Salt;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Users
     */
    public function setPassword($password)
    {
        $this->Password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * Set isVerified
     *
     * @param boolean $isVerified
     *
     * @return Users
     */
    public function setIsVerified($isVerified)
    {
        $this->IsVerified = $isVerified;
    
        return $this;
    }

    /**
     * Get isVerified
     *
     * @return boolean
     */
    public function getIsVerified()
    {
        return $this->IsVerified;
    }

    /**
     * Set verificationToken
     *
     * @param string $verificationToken
     *
     * @return Users
     */
    public function setVerificationToken($verificationToken)
    {
        $this->VerificationToken = $verificationToken;
    
        return $this;
    }

    /**
     * Get verificationToken
     *
     * @return string
     */
    public function getVerificationToken()
    {
        return $this->VerificationToken;
    }

    /**
     * Set reputation
     *
     * @param integer $reputation
     *
     * @return Users
     */
    public function setReputation($reputation)
    {
        $this->Reputation = $reputation;
    
        return $this;
    }

    /**
     * Get reputation
     *
     * @return integer
     */
    public function getReputation()
    {
        return $this->Reputation;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Users
     */
    public function setCreationDate($creationDate)
    {
        $this->CreationDate = $creationDate;
    
        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->CreationDate;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Sites $site
     *
     * @return Users
     */
    public function setSite(\AppBundle\Entity\Sites $site = null)
    {
        $this->Site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Sites
     */
    public function getSite()
    {
        return $this->Site;
    }
    
    
    // Added manually
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->Id,
            $this->Username,
            $this->Password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->Id,
            $this->Username,
            $this->Password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
}
