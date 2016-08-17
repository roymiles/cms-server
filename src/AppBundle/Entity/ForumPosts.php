<?php
// src/AppBundle/Entity/ForumPosts.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ForumPosts")
 */
class ForumPosts
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
 
     /**
     * @ORM\ManyToOne(targetEntity="ForumTopics", inversedBy="ForumPosts")
     * @ORM\JoinColumn(name="TopicId", referencedColumnName="Id")
     */
    private $Topic;  
    
    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="ForumPosts")
     * @ORM\JoinColumn(name="CreatorId", referencedColumnName="id")
     */
    private $Creator;    

    /**
     * @ORM\Column(type="text")
     */
    private $Post;
    
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
     * Set post
     *
     * @param string $post
     *
     * @return ForumPosts
     */
    public function setPost($post)
    {
        $this->Post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return string
     */
    public function getPost()
    {
        return $this->Post;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return ForumPosts
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
     * Set topic
     *
     * @param \AppBundle\Entity\ForumTopics $topic
     *
     * @return ForumPosts
     */
    public function setTopic(\AppBundle\Entity\ForumTopics $topic = null)
    {
        $this->Topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \AppBundle\Entity\ForumTopics
     */
    public function getTopic()
    {
        return $this->Topic;
    }

    /**
     * Set creator
     *
     * @param \AppBundle\Entity\Users $creator
     *
     * @return ForumPosts
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
