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
     * Set isApiUser
     *
     * @param string $isApiUser
     *
     * @return UserActions
     */
    public function setIsApiUser($isApiUser)
    {
        $this->IsApiUser = $isApiUser;
    
        return $this;
    }

    /**
     * Get isApiUser
     *
     * @return string
     */
    public function getIsApiUser()
    {
        return $this->IsApiUser;
    }

    /**
     * Set action
     *
     * @param \AppBundle\Entity\Actions $action
     *
     * @return UserActions
     */
    public function setAction(\AppBundle\Entity\Actions $action = null)
    {
        $this->Action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return \AppBundle\Entity\Actions
     */
    public function getAction()
    {
        return $this->Action;
    }
}
