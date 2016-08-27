<?php
// src/AppBundle/Entity/Sessions.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sessions")
 */
class Sessions
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $Token;

    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="Sessions")
     * @ORM\JoinColumn(name="UserId", referencedColumnName="Id")
     */
    private $User;   
        
    /**
     * @ORM\ManyToOne(targetEntity="Sites", inversedBy="Sessions")
     * @ORM\JoinColumn(name="SiteId", referencedColumnName="Id")
     */
    private $Site;

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
     * @return Sessions
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
     * @return Sessions
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

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Sites $site
     *
     * @return Sessions
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
}
