<?php
// src/AppBundle/Entity/Documentation.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Documentation")
 */
class Documentation
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Documentation", inversedBy="Documentation")
     * @ORM\JoinColumn(name="ParentDocumentationId", referencedColumnName="Id")
     */
    private $ParentDocumentation;
  
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
    private $PageContent;

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
     * @return Documentation
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
     * @return Documentation
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
     * Set pageContent
     *
     * @param string $pageContent
     *
     * @return Documentation
     */
    public function setPageContent($pageContent)
    {
        $this->PageContent = $pageContent;

        return $this;
    }

    /**
     * Get pageContent
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->PageContent;
    }

    /**
     * Set parentDocumentation
     *
     * @param \AppBundle\Entity\Documentation $parentDocumentation
     *
     * @return Documentation
     */
    public function setParentDocumentation(\AppBundle\Entity\Documentation $parentDocumentation = null)
    {
        $this->ParentDocumentation = $parentDocumentation;
    
        return $this;
    }

    /**
     * Get parentDocumentation
     *
     * @return \AppBundle\Entity\Documentation
     */
    public function getParentDocumentation()
    {
        return $this->ParentDocumentation;
    }
}
