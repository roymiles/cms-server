<?php

namespace AppBundle\Security;

use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    const GET = 'GET';
    const DELETE = 'DELETE';
    
    public function supports($attribute, $subject)
    {
        return $subject instanceof Users && in_array($attribute, array(
            self::GET, self::DELETE
        ));
    }
    
    protected function voteOnAttribute($attribute, $post, TokenInterface $token)
    {
        dump($attribute, $post, $token);die;
    }
}