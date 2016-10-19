<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Entity\Documentation;
use AppBundle\Services\Entities\DocumentationManager;

class DocumentationVoter extends Voter
{
    const GET = 'GET';
    const DELETE = 'DELETE';
    const EDIT = 'EDIT';
    
    public function __construct(DocumentationManager $DocumentationManager)
    {
        $this->DocumentationManager = $DocumentationManager;
    }
    
    public function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::GET, self::DELETE, self::EDIT))) {
            return false;
        }

        // only vote on Documentation objects inside this voter
        if (!$subject instanceof Documentation) {
            return false;
        }
  
        return true;
    }
    
    protected function voteOnAttribute($attribute, $object, TokenInterface $token)
    {
        // Get the current user
        $user = $token->getUser();
        switch ($attribute) {
            case self::GET:
                return $this->canGet($object, $user);
            case self::DELETE:
                return $this->canDelete($object, $user);
            case self::EDIT:
                return $this->canEdit($object, $user);
        }
        
        throw new \LogicException('This code should not be reached!');
    }
    
    private function canGet(Documentation $subject, $user){
        return true;
    }


    private function canDelete(Documentation $subject, $user){
        // Anonymous users dont have privileges
        if ($user instanceof AnonymousUser) {
            return false;
        }
        
        return false; // Cant delete documentations yet
    }   
    
    private function canEdit(Documentation $subject, $user){
        return true;
    }
}
