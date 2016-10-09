<?php
// src/AppBundle/Security/Api/TokenAuthenticator.php
namespace AppBundle\Security\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Services\Entities\UsersManager;
use AppBundle\Services\Entities\SitesManager;
use AppBundle\Services\Entities\AccessTokensManager;

use Symfony\Component\Routing\RouterInterface;

//use AppBundle\Entity\Users;
use AppBundle\Entity\AccessTokens;
use AppBundle\Security\AnonymousUser;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    
    private $em;
    private $session;
    private $UsersManager;
    private $SitesManager;
    private $AccessTokensManager;
    private $router;
    
    public function __construct(EntityManager $em, 
                                Session $session, 
                                UsersManager $UsersManager, 
                                SitesManager $SitesManager,
                                AccessTokensManager $AccessTokensManager,
                                RouterInterface $router)
    {
        $this->em = $em;
        $this->session = $session;
        $this->UsersManager = $UsersManager;
        $this->SitesManager = $SitesManager;
        $this->AccessTokensManager = $AccessTokensManager;
        $this->router = $router;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    private $isLoginRequest = false;
    public function getCredentials(Request $request)
    {
        if($request->getPathInfo() == '/api/login' && $request->isMethod('POST')){
            $this->isLoginRequest = true;
            // Login form submission
            return array(
              'Username' => $request->request->get('Username'),
              'Password' => $request->request->get('Password'),
              'SiteToken' => $request->request->get('site_token'),
            ); 
        }

        // Access token is a $_GET parameter (maybe chnge to header token?)
        if($request->query->has('access_token')){
            return array(
                'AccessToken' => $request->query->get('access_token')
            );  
        } 
        
        // User does not exist
        return array(); 
    }

    private $accessToken = false;
    public function getUser($credentials, UserProviderInterface $userProvider)
    {   
        // if null, authentication will fail   
        if(empty($credentials)){
            return new AnonymousUser();
        }   
        
        
        if(array_key_exists('Username', $credentials) && array_key_exists('Password', $credentials)){
            // Get site
            $Site = $this->SitesManager->get(['Token' => $credentials['SiteToken']], ['limit' => 1]);
            // Login form
            return $this->UsersManager->get(['Username' => $credentials['Username'], 'Site' => $Site], ['limit' => 1]);
        }        
        
        // External site maintaining login state through access token
        if(array_key_exists('AccessToken', $credentials)){
            $this->accessToken = true;
            $accessTokenObject = $this->AccessTokensManager->get(['Token' => $credentials['AccessToken']], ['limit' => 1]);
            if($accessTokenObject instanceof AccessTokens){
                return $accessTokenObject->getUser();
            }else{
                return null;
            }
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {     
        if($user instanceof AnonymousUser){
            //echo "AnonymousUser";
            return true;
        }
        
        if(array_key_exists('Password', $credentials)){
            if(password_verify($credentials['Password'], $user->getPassword())){
                // Valid credentials
                // Should be for external sites only
                if($user->getSite()->getId() != -1){
                    $AccessToken = $this->AccessTokensManager->add($user);
                    return true;
                }else{
                    return false;
                }
            }
        }        
          
        if($this->accessToken){
            if($user instanceof Users){
                return true;
            }else{
                return false;
            }
        }
        
        return false;

    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if($this->isLoginRequest){
            return new RedirectResponse($token->getUser()->getSite()->getDomainName() . '?site_token=' . $request->request->get('site_token'));
        }else{
            // on success, let the request continue
            return null;
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if($this->isLoginRequest){
            $this->session->getFlashBag()->add('login-errors', strtr($exception->getMessageKey(), $exception->getMessageData()));
            return new RedirectResponse($this->router->generate('Login', array('site_token' => $request->request->get('site_token'))));
        }else{
            return new JsonResponse(array('Error' => 'Authentication failure'), Response::HTTP_FORBIDDEN); 
        }
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