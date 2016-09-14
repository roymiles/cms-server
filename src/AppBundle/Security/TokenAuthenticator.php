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

use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Services\UsersManager;
use AppBundle\Services\SitesManager;
use AppBundle\Services\AccessTokensManager;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

//use AppBundle\Entity\Users;
//use AppBundle\Entity\AnonymousUser;

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
    public function getCredentials(Request $request)
    {
        if($request->getPathInfo() == '/login' && $request->isMethod('POST')){
            // Login form submission
            return array(
              'Username' => $request->request->get('Username'),
              'Password' => $request->request->get('Password'),
              'SiteToken' => $request->request->get('site_token'),
            ); 
        }
        
        if($request->request->has('AccessToken')){
            return array(
                'AccessToken' => $request->request->get('AccessToken')  
            );
        }else{
            //$csrf_token = $request->request->get('csrf_token', '');
            $User = $this->session->get('User');   
            if($User instanceof UserInterface /*&& valid($csrf_token)*/){
                // User session attribute exists
                return array(
                    'UserId' => $User->getId(),
                    'LoginString' => $this->session->get('LoginString')
                );  
            } 
        }
            
        // User does not exist
        return array();
        
    }

    private $isSession = false;
    private $accessToken = false;
    public function getUser($credentials, UserProviderInterface $userProvider)
    {   
        // if null, authentication will fail   
        if(empty($credentials)){
            return new AnonymousUser();
        }
        
        if(array_key_exists('Username', $credentials) && array_key_exists('Password', $credentials)){
            // Get site ID
            $Site = $this->SitesManager->get(['Token' => $credentials['SiteToken']], ['limit' => 1]);
            // Login form
            return $this->UsersManager->get(['Username' => $credentials['Username'], 'Site' => $Site], ['limit' => 1]);
        }
        
        // Local site maintaining login state through session variables
        if(array_key_exists('UserId', $credentials)){
            // Session credentials
            $this->isSession = true;
            return $this->UsersManager->get(['Id' => $credentials['UserId']], ['limit' => 1]);
        }    
        
        // External site maintaining login state through access token
        if(array_key_exists('AccessToken', $credentials)){
            $this->accessToken = true;
            return $this->AccessTokensManager->get(['Token' => $credentials['AccessToken']], ['limit' => 1])->getUser();
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
                if($user->getSite()->getId() != -1){
                    // External site, so set access token
                    $this->accessToken = $this->AccessTokensManager->add($user);
                    dump($this->accessToken);die;
                    return true;
                }else{
                    $this->session->set('User', $user);
                    $this->session->set('isLoggedIn', true);
                    $this->session->set('LoginString', hash('sha512', $user->getPassword().$_SERVER['HTTP_USER_AGENT']));
                    return true;
                }
            }
        }
        
        if($this->isSession){
            $Password = $user->getPassword();
            $loginCheck = hash('sha512', $Password.$_SERVER['HTTP_USER_AGENT']);
            
            $UserId = $credentials['UserId'];
            $loginString = $credentials['LoginString'];
            
            if($UserId == null || $loginString == null || $loginCheck == null){
                return false;
            }
            
            if(hash_equals($loginCheck, $loginString)){
                
                // Update user session object
                $this->session->set('User', $user);

                // For easy access in TWIG
                $this->session->set('isLoggedIn', true);

                // Return true to cause authentication success
                return true;
            }else{
                return false;
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
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        die('onAuthenticationFailure');
        $this->session->getFlashBag()->add('banner-error', strtr($exception->getMessageKey(), $exception->getMessageData()));
        return new RedirectResponse($this->router->generate('Login'));
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