<?php
// src/AppBundle/Entity/Users.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 */
class Users implements UserInterface, \Serializable
{
    public function __construct()
    {
        $this->CreationDate = new \DateTime(); 
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $Id;  

    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 4,
     *      max = 50,
     *      minMessage = "Username must be at least {{ limit }} characters long",
     *      maxMessage = "Username name cannot be longer than {{ limit }} characters"
     * )
     */
    private $Username;  
    
    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\Email()
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 256,
     *      minMessage = "Email must be at least {{ limit }} characters long",
     *      maxMessage = "Email cannot be longer than {{ limit }} characters"
     * )
     */
    private $Email;  
    
    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 8,
     *      max = 100,
     *      minMessage = "Password must be at least {{ limit }} characters long",
     *      maxMessage = "Password cannot be longer than {{ limit }} characters"
     * )
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
     * @Assert\NotBlank()
     */
    private $Site;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserRoles", inversedBy="Users")
     * @ORM\JoinColumn(name="Roles", referencedColumnName="Id")
     */
    private $Roles;
    
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
    
    
    public function getRoles(){
        return array('ROLE_USER');
    }

    // This is for removing sensitive credentials
    // http://stackoverflow.com/questions/8455398/symfony-2-logout-userinterfaceerasecredentials
    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        /*
         *  Does not need all these fields - can be removed later
         *   http://symfony.com/doc/current/security/entity_provider.html#what-do-the-serialize-and-unserialize-methods-do
         */
        return serialize(array(
            $this->Id,
            $this->Username,
            $this->Email,
            $this->Password,
            $this->IsVerified,
            $this->VerificationToken,
            $this->Reputation,
            $this->Site,
            $this->Roles,
            $this->CreationDate
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->Id,
            $this->Username,
            $this->Email,
            $this->Password,
            $this->IsVerified,
            $this->VerificationToken,
            $this->Reputation,
            $this->Site,
            $this->Roles,
            $this->CreationDate
        ) = unserialize($serialized);
    }

    /**
     * Set roles
     *
     * @param \AppBundle\Entity\UserRoles $roles
     *
     * @return Users
     */
    public function setRoles(\AppBundle\Entity\UserRoles $roles = null)
    {
        $this->Roles = $roles;
    
        return $this;
    }  
}
