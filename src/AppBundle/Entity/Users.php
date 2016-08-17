<?php
// src/AppBundle/Entity/Users.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 */
class Users extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $SiteId;    

    public function __construct()
    {
        parent::__construct();
        $this->SiteId = -1;
    }

    /**
     * Set siteId
     *
     * @param integer $siteId
     *
     * @return Users
     */
    public function setSiteId($siteId)
    {
        $this->SiteId = $siteId;

        return $this;
    }

    /**
     * Get siteId
     *
     * @return integer
     */
    public function getSiteId()
    {
        return $this->SiteId;
    }
}
