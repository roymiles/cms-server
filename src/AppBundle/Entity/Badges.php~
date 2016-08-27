<?php
// src/AppBundle/Entity/Badges.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Badges")
 */
class Badges
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
    private $Name;  
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="Badges")
     * @ORM\JoinColumn(name="CreatorId", referencedColumnName="Id")
     */
    private $Creator;    

    /**
     * @ORM\Column(type="text")
     */
    private $Description;
    
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
     * Set name
     *
     * @param string $name
     *
     * @return Badges
     */
    public function setName($name)
    {
        $this->Name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Badges
     */
    public function setDescription($description)
    {
        $this->Description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Badges
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
     * Set creator
     *
     * @param \AppBundle\Entity\Users $creator
     *
     * @return Badges
     */
    public function setCreator(\AppBundle\Entity\Users $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \AppBundle\Entity\Users
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}
