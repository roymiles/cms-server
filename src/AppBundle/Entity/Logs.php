<?php
// src/AppBundle/Entity/Logs.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Logs")
 */
class Logs
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
     /**
      * @ORM\Column(type="text")
      */
    private $Message;    
    
     /**
      * @ORM\Column(type="text")
      */
    private $Data;   
    
     /**
      * @ORM\Column(type="datetime")
      */
    private $DateTime;

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
     * @return Logs
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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return Logs
     */
    public function setDateTime($dateTime)
    {
        $this->DateTime = $dateTime;
    
        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->DateTime;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Logs
     */
    public function setData($data)
    {
        $this->Data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->Data;
    }
}
