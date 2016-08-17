<?php
// src/AppBundle/Entity/Modules.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Modules")
 */
class Modules
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="Modules")
     * @ORM\JoinColumn(name="CreatorId", referencedColumnName="id")
     */
    private $Creator;
  
    /**
     * @ORM\Column(type="string", length=256)
     */
    private $Name;    
    
    /**
     * @ORM\Column(type="date")
     */
    private $CreationDate;    

    /**
     * @ORM\Column(type="text")
     */
    private $WikiPost;

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
     * @return Modules
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Modules
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
     * Set wikiPost
     *
     * @param string $wikiPost
     *
     * @return Modules
     */
    public function setWikiPost($wikiPost)
    {
        $this->WikiPost = $wikiPost;

        return $this;
    }

    /**
     * Get wikiPost
     *
     * @return string
     */
    public function getWikiPost()
    {
        return $this->WikiPost;
    }

    /**
     * Set creator
     *
     * @param \AppBundle\Entity\Users $creator
     *
     * @return Modules
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
