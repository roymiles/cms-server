<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiSecurityController extends Controller
{
      
    /**
     * Process the login request by the API
     * * The login is authenticated through OAUTH. If valid credentials, 
     * * the controller redirects to the valid URL with the access token
     * * in the GET parameter
     * @Route("/api/{SiteToken}/login", name="ApiLogin")
     * @Method({"POST"})
     */
    public function apiLoginAction(Request $request, $SiteToken)
    { 
        $Username = $request->request->get('username', '');
        $Password = $request->request->get('password', '');

        $SitesManager = $this->get('app.SitesManager');
        $UsersManager = $this->get('app.UsersManager');
        
        if($SitesManager->isExternalSite($SiteToken)){
            // Valid external site (eg. not an internal login attempt)
            $Site = $SitesManager->getSiteByToken($SiteToken);
            $RedirectUrl = $request->request->get('redirect', '');
            if($SitesManager->isValidRedirect($RedirectUrl, $Site->Url)){
                // Valid redirect
                if($UsersManager->verifyCredentials($Username, $Password, $Site->Id)){
                    // Valid credentials
                    $SessionsManager = $this->get('app.SessionsManager');
                    $User = $UsersManager->getUserByUsername($Username, $Site->Id);
                    
                    $AccessToken = $SessionsManager->generateSession($User->Id, $Site->Id);
                
                    // Redirect the user
                    header('Location: '. $RedirectUrl .'?AccessToken=' . $AccessToken);
                }else{
                    // Invalid credentials
                }
                
            }else{
                // Invalid redirect
                // Warn user / log
                // This may be an attack to get the users credentials
                $User = $UserManager->getUserByUsername($Username, $Site->Id);
                $UsersManager->resetPasswordById($User->Id);
            }
        }else{
            // Unable to verify the site
            // Warn user / log
        }
    }

    /**
     * Verify the user information and add it to the database
     * @Route("/api/{token}/register", name="ApiRegister", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function apiRegisterAction(Request $request, $token)
    {
        $username = $request->request->get('username', '');
        $password = $request->request->get('password', '');
        $token = $request->request->get('token', '');
        $url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

        return new JsonResponse(array('username' => $username, 'password' => $password, 'token' => $token, 'url' => $url));
    }
    
    /**
     * Request that the password to be reset. An email will be sent to the user authenticating this
     * @Route("/api/{token}/forgotpassword", name="ApiForgotPassword", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function apiForgotPasswordAction(Request $request){
        
        
    }
    
    /**
     * Delete the user session from the database
     * @Route("/api/{SiteToken}/logout", name="ApiLogout", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function apiLogoutAction(Request $request, string $SiteToken){
        $AccessToken = $request->request->get('accessToken', '');
        
        $SessionsManager = $this->get('app.SessionsManager');
        if($SessionsManager->isValidSessionByAccessToken($AccessToken, $SiteToken)){
            // Valid accessToken / siteToken combination
            $SessionsManager->deleteSessionByAccessToken($AccessToken, $SiteToken);
        }else{
            // Invalid accessToken / siteToken combination
            // Don't log the user out
        }
    }        
    
}
