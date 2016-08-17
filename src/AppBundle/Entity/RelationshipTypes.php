<?php
// src/AppBundle/Entity/RelationshipTypes.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="RelationshipTypes")
 */
class RelationshipTypes
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
    private $TypeName;

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
     * Set typeName
     *
     * @param string $typeName
     *
     * @return RelationshipTypes
     */
    public function setTypeName($typeName)
    {
        $this->TypeName = $typeName;

        return $this;
    }

    /**
     * Get typeName
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->TypeName;
    }
}
