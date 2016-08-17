<?php
// src/AppBundle/Entity/ForumCategories.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ForumCategories")
 */
class ForumCategories
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="ForumCategories")
     * @ORM\JoinColumn(name="CreatorId", referencedColumnName="id")
     */
    private $Creator;    

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $CategoryName;
    
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
     * Set categoryName
     *
     * @param string $categoryName
     *
     * @return ForumCategories
     */
    public function setCategoryName($categoryName)
    {
        $this->CategoryName = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->CategoryName;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return ForumCategories
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
     * @return ForumCategories
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
