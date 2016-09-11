<?php

namespace AppBundle\Security;

use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Services\UsersManager;
use AppBundle\Services\SitesManager;

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
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::GET, self::DELETE))) {
            return false;
        }
        //dump($attribute, $subject);die;

        // only vote on User objects inside this voter
        if (!$subject instanceof Users) {
            if (is_array($subject)) {
                // array of User objects
                foreach($subject as $s){
                    // check if each array element is a User object
                    if (!$s instanceof Users) {
                        return false;
                    }
                }
            }else{
                return false;
            }
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
        // All the users in the $object should be of the same site
        if (!$subject instanceof Users) {
            if (is_array($subject)) {
                // array of User objects
                $SiteID = $subject[0]->getSite()->getId();
                foreach($subject as $s){
                    // all users should be part of same site
                    // + a single user can only have access to 1 site
                    if($s->getSite()->getId() != $SiteID){
                        // different site, so return false
                        return false;
                    }
                }
            }else{
                // Unknown error
                throw new \LogicException('This code should not be reached! 2.');
            }
        }else{
            $SiteID = $subject->getSite()->getID();
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
}