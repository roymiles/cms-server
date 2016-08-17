<?php
// src/AppBundle/Entity/Sites.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sites")
 */
class Sites
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="Sites")
     * @ORM\JoinColumn(name="OwnerId", referencedColumnName="id")
     */
    private $Owner;
  
    /**
     * @ORM\Column(type="string", length=256)
     */
    private $Token;    
    
    /**
     * @ORM\Column(type="date")
     */
    private $CreationDate;    
    
    /**
     * @ORM\Column(type="string", length=256)
     */
    private $Url;

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
     * @return Sites
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Sites
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
     * Set url
     *
     * @param string $url
     *
     * @return Sites
     */
    public function setUrl($url)
    {
        $this->Url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->Url;
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\Users $owner
     *
     * @return Sites
     */
    public function setOwner(\AppBundle\Entity\Users $owner = null)
    {
        $this->Owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\Users
     */
    public function getOwner()
    {
        return $this->Owner;
    }
}
