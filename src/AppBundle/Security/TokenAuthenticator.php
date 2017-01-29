<?php
// src/AppBundle/Security/TokenAuthenticator.php
namespace AppBundle\Security;

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

abstract class LoginType
{
    const UsernameAndPassword = 0;
    const AccessToken = 1;
    const SessionData = 2;
    const UNKNOWN = 3;
}

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    
    private $em;
    private $session;
    private $UsersManager;
    private $SitesManager;
    private $AccessTokensManager;
    private $router;
    
    private $loginType = LoginType::UNKNOWN;
    private $AccessToken; // Store the access token object
    
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
        /*
         * Logging in with username and password
         */
        if($request->getPathInfo() == '/login' && $request->isMethod('POST')){
            $this->loginType = LoginType::UsernameAndPassword;
            // Login form submission
            return array(
              'Username' => $request->request->get('Username'),
              'Password' => $request->request->get('Password'),
              'SiteToken' => $request->request->get('site_token'),
            ); 
        }

        /*
         * Logging in with access token
         * - Access token is a $_POST parameter (inside the request)
         */
        if($request->request->has('access_token')){
            $this->loginType = LoginType::AccessToken;
            return array(
                'AccessToken' => $request->request->get('access_token')
            );  
        } 
        
        /*
         * Logging in with session data
         */
        $User = $this->session->get('User');   
        if($User instanceof UserInterface){
            $this->loginType = LoginType::SessionData;
            // User session attribute exists
            return array(
                'UserId' => $User->getId(),
                'LoginString' => $this->session->get('LoginString')
            );  
        } 
        
        // User does not exist
        return array(); 
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {   
        // If null, authentication will fail   
        if(empty($credentials)){
            return new AnonymousUser();
        }   
        
        switch($this->loginType){
            
            case LoginType::UsernameAndPassword:
                /*
                 * Logging in with username and password fields
                 */
                if(array_key_exists('Username', $credentials) && array_key_exists('Password', $credentials)){
                    // Get site object using the token
                    $Site = $this->SitesManager->get(['Token' => $credentials['SiteToken']], ['limit' => 1]);
                    // Return the user from the given site
                    return $this->UsersManager->get(['Username' => $credentials['Username'], 'Site' => $Site], ['limit' => 1]);
                }   
                
                break;
                
            case LoginType::AccessToken: 
                /*
                 * Logging in using access token
                 */
                if(array_key_exists('AccessToken', $credentials)){
                    $this->accessToken = true;
                    $accessTokenObject = $this->AccessTokensManager->get(['Token' => $credentials['AccessToken']], ['limit' => 1]);
                    if($accessTokenObject instanceof AccessTokens){
                        return $accessTokenObject->getUser();
                    }else{
                        return null;
                    }
                } 
                
                break;

            case LoginType::SessionData:
                /*
                 * Logging in with session data
                 */
                throw new NotYetImplemented(
                    'Session authentication has not been implemented'
                );                
                
                if(array_key_exists('UserId', $credentials)){
                    return $this->UsersManager->get(['Id' => $credentials['UserId']], ['limit' => 1]);
                }
                
                break;
                
            default:
                /*
                 * Should not ever end up here
                 */
                throw new \LogicException('Invalid login type');
            
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {     
        if($user instanceof AnonymousUser){
            //echo "AnonymousUser";
            return true;
        }
        
        switch($this->loginType){
            
            case LoginType::UsernameAndPassword:        
                /*
                 * Logging in with username and password fields
                 */
                if(array_key_exists('Password', $credentials)){
                    if(password_verify($credentials['Password'], $user->getPassword())){
                        /*
                         *  Valid credentials
                         */
                        $this->AccessToken = $this->AccessTokensManager->add($user);
                        return true;
                    }else{
                        // Invalid password
                        return false;
                    }
                }else{
                    // Password not supplied
                    return false;
                } 
                
                break;
                
            case LoginType::AccessToken: 
                /*
                 * Logging in using access token
                 */
                if($user instanceof Users){
                    return true;
                }else{
                    return false;
                }
                   
                break;
                
            case LoginType::SessionData:
                /*
                 * Logging in with session data
                 */
                
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
                
                break;
                
            default:
                /*
                 * Should not ever end up here
                 */
                throw new \LogicException('Invalid login type');
        }
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

        if(LoginType::UsernameAndPassword){
            // Redirect after logging in with the site_token available as a GET parameter
            
            // Create the redirect url
            $url = $token->getUser()->getSite()->getDomainName();
            $parameters = array(
                                'site_token' => $request->request->get('site_token'),
                                'access_token' => $this->AccessToken->getToken()
                               );
            
            $redirect_url = $this->get('router')->generate($url, $parameters);
            return new RedirectResponse($redirect_url);
        }else{
            // On success, let the request continue
            return null;
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Flash message containing the login error message
        $this->session->getFlashBag()->add('login-errors', strtr($exception->getMessageKey(), $exception->getMessageData()));
        $redirect_url = $this->router->generate('Login', array('site_token' => $request->request->get('site_token')));
        return new RedirectResponse($redirect_url);        
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // You might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
