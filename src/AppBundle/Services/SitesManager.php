<?php
// src/AppBundle/Services/Api/SitesManager.php
namespace AppBundle\Services;

use AppBundle\Entity\Sites;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManager;

class SitesManager
{
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle:Entity:Api:Sites';
    }
    
    // Return the site object
    public function getSiteById(int $id)
    {
        $site = $this->getDoctrine()
            ->getRepository($this->repository)
            ->find($id);
    
        if (!$site) {
            throw $this->createNotFoundException(
                'No site found for id '.$id
            );
        }
    
        return $site;
    }
    
    public function getSiteByUserID(int $userId)
    {
        $site = $this->getDoctrine()
            ->getRepository($this->repository)
            ->findByUserId($siteId);
    
        if (!$site) {
            throw $this->createNotFoundException(
                'No site found for id '.$siteId
            );
        }
    
        return $site;    
    }
    
    public function getSiteByToken(string $token)
    {
        $site = $this->getDoctrine()
            ->getRepository($this->repository)
            ->findByToken($token);
    
        if (!$site) {
            throw $this->createNotFoundException(
                'No site found for id '.$siteId
            );
        }
    
        return $site;    
    }    
    
    public function getSiteByUrl(string $url)
    {
        $site = $this->getDoctrine()
            ->getRepository($this->repository)
            ->findByURL($url);
    
        if (!$site) {
            throw $this->createNotFoundException(
                'No site found for id '.$siteId
            );
        }
    
        return $site;    
    }     
    
    public function deleteSiteById(int $id){}
    public function deleteSiteByUserID(int $userId){}
    public function deleteSiteByToken(string $token){}
    public function deleteSiteByUrl(string $url){}
    
    public function updateSiteByID(int $id){}
    public function updateSiteByUserID(int $userId){}
    public function updateSiteByURL(string $url){}
}
