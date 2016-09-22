<?php
// src/AppBundle/Entity/SiteModules.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteModules")
 */
class SiteModules
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Modules", inversedBy="SiteModules")
     * @ORM\JoinColumn(name="Module", referencedColumnName="Id")
     */
    private $Module;
  
    /**
     * @ORM\ManyToOne(targetEntity="Sites", inversedBy="Modules")
     * @ORM\JoinColumn(name="Site", referencedColumnName="Id")
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
     * Set module
     *
     * @param \AppBundle\Entity\Modules $module
     *
     * @return SiteModules
     */
    public function setModule(\AppBundle\Entity\Modules $module = null)
    {
        $this->Module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return \AppBundle\Entity\Modules
     */
    public function getModule()
    {
        return $this->Module;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Sites $site
     *
     * @return SiteModules
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
