<?php
// src/AppBundle/Services/DocumentationManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Documentation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class DocumentationManager
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\Documentation';
    }
    
    public function get(array $options){
        $doc = $this->em
                    ->getRepository($this->repository)
                    ->findBy($options);

        if (!$doc) {
            return false;
        }
    
        return $doc; 
    }
}
