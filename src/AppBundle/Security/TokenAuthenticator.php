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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

//use AppBundle\Entity\Users;
//use AppBundle\Entity\AnonymousUser;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    
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
        //dump($request);die;
        if($request->getPathInfo() == '/login' && $request->isMethod('POST')){
            // Login form submission
            return array(
              'Username' => $request->request->get('Username'),
              'Password' => $request->request->get('Password'),
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

    public function getUser($credentials, UserProviderInterface $userProvider)
    {   
        //dump($credentials);die();
        /*$UserId = $credentials['UserId'];
        
        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $this->UsersManager->get(['Id' => $UserId], ['limit' => 1]);*/
        
        if(empty($credentials)){
            return new AnonymousUser();
        }
        
        if(array_key_exists('Username', $credentials) && array_key_exists('Password', $credentials)){
            // Login form
            return $this->UsersManager->get(['Username' => $credentials['Username']], ['limit' => 1]);
        }
        
        if(array_key_exists('UserId', $credentials)){
            // Login form
            return $this->UsersManager->get(['Id' => $credentials['UserId']], ['limit' => 1]);
        }    
        
        /*try {
          //return $userProvider->loadUserByUsername($credentials['username']);
          return $this->UsersManager->get(['Username' => $credentials['Username']], ['limit' => 1]);
        }
        catch (UsernameNotFoundException $e) {
          throw new CustomUserMessageAuthenticationException("eh");
        }*/
    }

    public function checkCredentials($credentials, UserInterface $user)
    {     
        //$this->session->set('User', $user);
        return true;
        //dump($user, $credentials);die();
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        /*$loginString = $credentials['LoginString'];
        $UserId = $credentials['UserId'];
        
        $password = $this->UsersManager->get(['Id' => $UserId], ['limit' => 1])->getPassword();
        $loginCheck = hash('sha512', $password.$_SERVER['HTTP_USER_AGENT']);
        
        if($loginString === null){
            return false;
        }
        
        if(hash_equals($loginCheck, $loginString)){
            //dump($user);die;
            // Update user session object
            $this->session->set('User', $user);
            
            // For easy access in TWIG
            $this->session->set('isLoggedIn', true);
            
            // Return true to cause authentication success
            return true;
        }else{
            return false;
        }*/
        /*if ($user->getPassword() === $credentials['Password']) {
            return true;
        }
            throw new CustomUserMessageAuthenticationException("beh");
        }*/
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        die('error');
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