<?php
// src/AppBundle/Services/SiteModulesManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Sites;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class SiteModulesManager
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    // Return all the modules used by the Site
    public function getModulesBySiteId(int $siteId){}
    public function getModulesByToken(string $token){}
}
