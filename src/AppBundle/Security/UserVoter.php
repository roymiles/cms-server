<?php

namespace AppBundle\Security;

use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Services\Entities\UsersManager;
use AppBundle\Services\Entities\SitesManager;

class UserVoter extends Voter
{
    const GET = 'GET';
    const DELETE = 'DELETE';
    
    public function __construct(UsersManager $UsersManager, SitesManager $SitesManager)
    {
        $this->UsersManager = $UsersManager;
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
         *  Only vote on User objects inside this voter
         */
        if (!$subject instanceof Users) {
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
        
        /*
         * Should not reach here
         */
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
         *  The subject must be a Users object
         */
        if (!$subject instanceof Users) {
            return false;
        }   
        
        /*
         *  Check if the user object is the same object as the current logged in user,
         *  in which case the user is authorised
         */
        if($subject->getId() == $user->getId()){
            return true;
        }  
        
        /*
         *  Check if the logged in user owns the site for which the subject is part of
         */
        $SiteId = $subject->getSite()->getId();
        
        /*
         *  User should be the owner of the site corresponding to the user subjects
         */
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
        
        /*
         *  The subject must be a Users object
         */
        if (!$subject instanceof Users) {
            return false;
        }    
        
        /*
         *  Cant delete themself (yet)
         */
        if($subject->getId() == $user->getId()){
            return false;
        }
        
        /*
         *  Check if the logged in user owns the site for which the subject is part of
         */
        $SiteId = $subject->getSite()->getId();
        
        /*
         *  User should be the owner of the site corresponding to the user subjects
         */
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
}
