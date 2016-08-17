<?php
// src/AppBundle/Services/SessionsManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Sites;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class SessionsManager
{
    
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle:Entity:Users';
    }
    
    //-----------------------------------------------------
    // SELECT actions
    //-----------------------------------------------------      
    
    // Return the session object
    public function getSessionById(int $Id){
        $session = $this->getDoctrine()
            ->getRepository($this->repository)
            ->find($Id);
    
        if (!$session) {
            throw $this->createNotFoundException(
                'No session found for id '.$Id
            );
        }
    
        return $session;     
    }
    
    public function getSessionByUserId(int $UserId, int $SiteId){
        $query = $this->em->createQuery(
                'SELECT s
                FROM :repository AS s
                JOIN AppBundle:Entity:Users AS u WITH u.id = s
                JOIN AppBundle:Entity:Sites AS si WITH si.id = s
                WHERE si.id = :SiteId AND u.id = :UserId
                LIMIT 1'
        )->setParameter('repository', $this->repository)
         ->setParameter('SiteId', $SiteId)
         ->setParameter('UserId', $UserId);
         
        $session = $query->getResult();
    
        if (!$session) {
            throw $this->createNotFoundException(
                'No session found for UserId '.$UserId
            );
        }
    
        return $session;        
    }
    
    public function getSessionByAccessToken(string $AccessToken, int $SiteId){}
    
    //-----------------------------------------------------
    // INSERT actions
    //-----------------------------------------------------      
    
    // Create a new user session, must ensure the user is not logged in on multiple devices
    public function createSession(int $UserId, int $SiteId){}
    
    //-----------------------------------------------------
    // DELETE actions
    //-----------------------------------------------------      
    
    // Remove the user session from the database (effectively logging out a user)
    public function deleteSessionById(int $Id){}
    public function deleteSessionByUserId(int $UserId, int $SiteId){}
    public function deleteSessionByAccessToken(string $AccessToken, int $SiteId){}
    
    //-----------------------------------------------------
    // VALIDATION
    //-----------------------------------------------------      
    
    // Check if the session is still valid (will typically delete session if not)
    public function hasSessionExpired($Session){}
    public function hasSessionExpiredById(int $Id){}
    public function hasSessionExpiredByUserId(int $UserId, int $SiteId){}
    public function hasSessionExpiredByAccessToken(string $AccessToken, int $SiteId){}
    
    // Checks its length / character set etc
    public function isValidAccessToken(string $AccessToken){}
}
