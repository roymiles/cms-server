<?php
// src/AppBundle/Entity/UserRelationships.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="UserRelationships")
 */
class UserRelationships
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="UserRelationships")
     * @ORM\JoinColumn(name="RelatingUserId", referencedColumnName="Id")
     */
    private $RelatingUser;  
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="UserRelationships")
     * @ORM\JoinColumn(name="RelatedUserId", referencedColumnName="Id")
     */
    private $RelatedUser;    

    /**
     * @ORM\ManyToOne(targetEntity="RelationshipTypes", inversedBy="UserRelationships")
     * @ORM\JoinColumn(name="TypeId", referencedColumnName="Id")
     */
    private $Type;
    
    /**
     * @ORM\Column(type="date")
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return UserRelationships
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
     * Set relatingUser
     *
     * @param \AppBundle\Entity\Users $relatingUser
     *
     * @return UserRelationships
     */
    public function setRelatingUser(\AppBundle\Entity\Users $relatingUser = null)
    {
        $this->RelatingUser = $relatingUser;

        return $this;
    }

    /**
     * Get relatingUser
     *
     * @return \AppBundle\Entity\Users
     */
    public function getRelatingUser()
    {
        return $this->RelatingUser;
    }

    /**
     * Set relatedUser
     *
     * @param \AppBundle\Entity\Users $relatedUser
     *
     * @return UserRelationships
     */
    public function setRelatedUser(\AppBundle\Entity\Users $relatedUser = null)
    {
        $this->RelatedUser = $relatedUser;

        return $this;
    }

    /**
     * Get relatedUser
     *
     * @return \AppBundle\Entity\Users
     */
    public function getRelatedUser()
    {
        return $this->RelatedUser;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\RelationshipTypes $type
     *
     * @return UserRelationships
     */
    public function setType(\AppBundle\Entity\RelationshipTypes $type = null)
    {
        $this->Type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\RelationshipTypes
     */
    public function getType()
    {
        return $this->Type;
    }
}
