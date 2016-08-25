<?php
// src/AppBundle/Entity/UserActions.php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="UserActions")
 */
class UserActions
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Actions", inversedBy="UserActions")
     * @ORM\JoinColumn(name="Action", referencedColumnName="Id")
     */
    private $Action;
    
    /**
     * @ORM\Column(type="text")
     */
    private $IsApiUser;
}
