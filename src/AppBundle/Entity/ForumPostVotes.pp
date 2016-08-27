<?php
// src/AppBundle/Entity/ForumPostVotes.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ForumPostVotes")
 */
class ForumPostVotes
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="ForumPostVotes")
     * @ORM\JoinColumn(name="VoterId", referencedColumnName="Id")
     */
    private $Voter;    

    /**
     * @ORM\Column(type="int")
     */
    private $Value;
    
    /**
     * @ORM\Column(type="date")
     */
    private $VoteDate;    
}
