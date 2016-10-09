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
        //dump($attribute, $subject);die;
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::GET, self::DELETE))) {
            return false;
        }

        // only vote on User objects inside this voter
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
        
        throw new \LogicException('This code should not be reached! 1.');
    }
    
    private function canGet($subject, $user){
        // Anonymous users dont have privileges
        if ($user instanceof AnonymousUser) {
            return false;
        }
        // All the users in the $object should be of the same site
        if ($subject instanceof Users) {
            $SiteID = $subject->getSite()->getID();
        }else{
            return false;
        }
        
        // user should be the owner of the site corresponding to the user subjects
        $owner = $this->SitesManager->get(['Id' => $SiteID], ['limit' => 1])->getOwner();
        if($this->UsersManager->isEqual($owner, $user)){
            // the user is the owner of the site
            return true;
        }else{
            return false;
        }
    }


    private function canDelete(Users $subject, $user){
        // Anonymous users dont have privileges
        if ($user instanceof AnonymousUser) {
            return false;
        }
        
        // Cant delete themself
        if($subject->getId() == $user->getId()){
            return false;
        }
        
        // Get the site the user belongs to
        $SiteID = $subject->getSite()->getId();
        
        // user should be the owner of the site corresponding to the user subjects
        $owner = $this->SitesManager->get(['Id' => $SiteID], ['limit' => 1])->getOwner();
        if($this->UsersManager->isEqual($owner, $user)){
            // the user is the owner of the site
            return true;
        }else{
            return false;
        }
    }    
}
