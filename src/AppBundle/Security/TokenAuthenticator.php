<?php
// src/AppBundle/Security/TokenAuthenticator.php
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

//use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Services\UsersManager;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    //use ContainerAwareTrait;
    
    private $em;
    private $session;
    private $UsersManager;
    private $router;
    
    public function __construct(EntityManager $em, Session $session, UsersManager $UsersManager, RouterInterface $router)
    {
        $this->em = $em;
        $this->session = $session;
        $this->UsersManager = $UsersManager;
        $this->router = $router;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        $User = $this->session->get('User');
        return array(
            'UserId' => $User->getId(),
            'LoginString' => $this->session->get('LoginString')
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $UserId = $credentials['UserId'];
        
        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $this->UsersManager->get(['Id' => $UserId], ['limit' => 1]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        $loginString = $credentials['LoginString'];
        $UserId = $credentials['UserId'];
        
        $password = $this->UsersManager->get(['Id' => $UserId], ['limit' => 1])->getPassword();
        $loginCheck = hash('sha512', $password.$_SERVER['HTTP_USER_AGENT']);
        
        if($loginString === null){
            return false;
        }
        
        if(hash_equals($loginCheck, $loginString)){
            // return true to cause authentication success
            return true;
        }else{
            return false;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->session->getFlashBag()->add('banner-error', strtr($exception->getMessageKey(), $exception->getMessageData()));
        return new RedirectResponse($this->router->generate('Homepage'));
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}