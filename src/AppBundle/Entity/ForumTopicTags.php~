<?php
// src/AppBundle/Entity/ForumTopicTags.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ForumTopicTags")
 */
class ForumTopicTags
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $Id;
 
     /**
     * @ORM\ManyToOne(targetEntity="ForumTopics", inversedBy="ForumTopicTags")
     * @ORM\JoinColumn(name="TopicId", referencedColumnName="Id")
     */
    private $Topic;  
    
    /**
     * @ORM\ManyToOne(targetEntity="Tags", inversedBy="ForumTopicTags")
     * @ORM\JoinColumn(name="TagId", referencedColumnName="Id")
     */
    private $Tag;    

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
     * Set topic
     *
     * @param \AppBundle\Entity\ForumTopics $topic
     *
     * @return ForumTopicTags
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
     * Set tag
     *
     * @param \AppBundle\Entity\Tags $tag
     *
     * @return ForumTopicTags
     */
    public function setTag(\AppBundle\Entity\Tags $tag = null)
    {
        $this->Tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \AppBundle\Entity\Tags
     */
    public function getTag()
    {
        return $this->Tag;
    }
}
