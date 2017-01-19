<?php

namespace AppBundle\Security;

use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Services\Entities\SitesManager;

class SiteVoter extends Voter
{
    const GET = 'GET';
    const DELETE = 'DELETE';
    
    public function __construct(SitesManager $SitesManager)
    {
        $this->SitesManager = $SitesManager;
    }
    
    public function supports($attribute, $subject)
    {
        /*
         *  If the attribute isn't one we support, return false
         */
        if (!in_array($attribute, array(self::GET, self::DELETE))) {
            return false;
        }

        /*
         *  Only vote on Site objects inside this voter
         */
        if (!$subject instanceof Sites) {
            return false;
        }
  
        return true;
    }
    
    protected function voteOnAttribute($attribute, $object, TokenInterface $token)
    {
        $user = $token->getUser();
        switch ($attribute) {
            case self::GET:
                return $this->canGet($object, $user);
            case self::DELETE:
                return $this->canDelete($object, $user);
        }
        
        throw new \LogicException('This code should not be reached!');
    }
    
    private function canGet($subject, $user){
        /*
         *  Anonymous users dont have privileges
         */
        if ($user instanceof AnonymousUser) {
            return false;
        }
        
        /*
         *  The subject must be a Sites object
         */
        if (!$subject instanceof Sites) {
            return false;
        }   
        
        /*
         *  Check if the user owns the site
         */
        $SiteId = $subject->getId();
        $owner = $this->SitesManager->get(['Id' => $SiteId], ['limit' => 1])->getOwner();
        if($this->UsersManager->isEqual($owner, $user)){
            /*
             *  The user is the owner of the site
             */
            return true;
        }else{
            return false;
        }
    }


    private function canDelete(Users $subject, $user){
        /*
         *  Anonymous users dont have privileges
         */
        if ($user instanceof AnonymousUser) {
            return false;
        }
        
        // TODO: needs to be implemented
        
        return false; // Cant delete sites yet
    }    
}
