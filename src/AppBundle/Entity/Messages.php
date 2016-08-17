<?php
// src/AppBundle/Entity/Messages.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Messages")
 */
class Messages
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="Messages")
     * @ORM\JoinColumn(name="SenderId", referencedColumnName="id")
     */
    private $Sender;
  
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="Messages")
     * @ORM\JoinColumn(name="ReceiverId", referencedColumnName="id")
     */
    private $Receiver;   
    
    /**
     * @ORM\Column(type="text")
     */
    private $Message;    
    
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
     * Set message
     *
     * @param string $message
     *
     * @return Messages
     */
    public function setMessage($message)
    {
        $this->Message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->Message;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Messages
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
     * Set sender
     *
     * @param \AppBundle\Entity\Users $sender
     *
     * @return Messages
     */
    public function setSender(\AppBundle\Entity\Users $sender = null)
    {
        $this->Sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \AppBundle\Entity\Users
     */
    public function getSender()
    {
        return $this->Sender;
    }

    /**
     * Set receiver
     *
     * @param \AppBundle\Entity\Users $receiver
     *
     * @return Messages
     */
    public function setReceiver(\AppBundle\Entity\Users $receiver = null)
    {
        $this->Receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \AppBundle\Entity\Users
     */
    public function getReceiver()
    {
        return $this->Receiver;
    }
}
