<?php
// src/AppBundle/Security/Web/TokenAuthenticator.php
namespace AppBundle\Security\Web;

use Symfony\Component\HttpFoundation\Request;
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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

//use AppBundle\Entity\Users;
use AppBundle\Security\AnonymousUser;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    
    private $em;
    private $session;
    private $UsersManager;
    private $SitesManager;
    private $router;
    
    public function __construct(EntityManager $em, 
                                Session $session, 
                                UsersManager $UsersManager, 
                                SitesManager $SitesManager,
                                RouterInterface $router)
    {
        $this->em = $em;
        $this->session = $session;
        $this->UsersManager = $UsersManager;
        $this->SitesManager = $SitesManager;
        $this->router = $router;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    private $userAgent;
    public function getCredentials(Request $request)
    {
        $this->userAgent = $request->headers->get('User-Agent', '0');
        if($request->getPathInfo() == '/login' && $request->isMethod('POST')){
            // Login form submission
            return array(
              'Username' => $request->request->get('Username'),
              'Password' => $request->request->get('Password'),
              'SiteToken' => $request->request->get('site_token'),
            ); 
        }
        
        $User = $this->session->get('User');   
        if($User instanceof UserInterface){
            // User session attribute exists
            return array(
                'UserId' => $User->getId(),
                'LoginString' => $this->session->get('LoginString')
            );  
        } 
            
        // User does not exist
        return array();
        
    }

    private $isSession = false;
    public function getUser($credentials, UserProviderInterface $userProvider)
    {   
        // if null, authentication will fail   
        if(empty($credentials)){
            return new AnonymousUser();
        }
        
        if(array_key_exists('Username', $credentials) && array_key_exists('Password', $credentials)){
            // Get site
            $Site = $this->SitesManager->get(['Token' => 'a'], ['limit' => 1]);
            // Login form
            return $this->UsersManager->get(['Username' => $credentials['Username'], 'Site' => $Site], ['limit' => 1]);
        }
        
        // Local site maintaining login state through session variables
        if(array_key_exists('UserId', $credentials)){
            // Session credentials
            $this->isSession = true;
            return $this->UsersManager->get(['Id' => $credentials['UserId']], ['limit' => 1]);
        }    
    }

    public function checkCredentials($credentials, UserInterface $user)
    {     
        if($user instanceof AnonymousUser){
            return true;
        }
        
        if(array_key_exists('Password', $credentials)){
            if(password_verify($credentials['Password'], $user->getPassword())){
                // Valid credentials
                // Should be for the local site only
                if($user->getSite()->getId() === -1){
                    $this->session->set('User', $user);
                    $this->session->set('isLoggedIn', true);
                    $this->session->set('LoginString', hash('sha512', $user->getPassword().$this->userAgent));
                    return true;
                }else{
                    return false;
                }
            }
        }
        
        if($this->isSession){
            $Password = $user->getPassword();
            $loginCheck = hash('sha512', $Password.$this->userAgent);
            
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
        
        return false;

    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        //$this->session->getFlashBag()->add('banner-notice', 'Logged in successfully');
        //return new RedirectResponse($this->router->generate('Homepage'));
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
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