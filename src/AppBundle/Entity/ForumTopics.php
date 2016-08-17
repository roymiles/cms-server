<?php
// src/AppBundle/Entity/ForumTopics.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ForumTopics")
 */
class ForumTopics
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="ForumTopics")
     * @ORM\JoinColumn(name="CreatorId", referencedColumnName="id")
     */
    private $Creator;    

    /**
     * @ORM\Column(type="text")
     */
    private $Body;
    
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
     * Set body
     *
     * @param string $body
     *
     * @return ForumTopics
     */
    public function setBody($body)
    {
        $this->Body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->Body;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return ForumTopics
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
     * @return ForumTopics
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
