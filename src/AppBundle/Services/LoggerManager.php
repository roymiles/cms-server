<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class LoggerManager extends Controller
{   
    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em            = $em;
    }    
    
    public function error($request, $token){}
}
