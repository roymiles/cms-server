<?php
// src/AppBundle/Services/AuthenticationManager.php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Session\Session;

class AuthenticationManager
{
    private $session;
    private $em;
    
    private $doOriginCheck;
    public function __construct(EntityManager $em, Session $session)
    {
        $this->em            = $em;
        $this->session       = $session;
        $this->doOriginCheck = false;
    }
    
    /**
     * Check CSRF tokens match between session and $origin. 
     * Make sure you generated a token in the form before checking it.
     * https://github.com/BKcore/NoCSRF
     * 
     * @param String $key The session and $origin key where to find the token.
     * @param Mixed $origin The object/associative array to retreive the token data from (usually $_POST).
     * @param Boolean $throwException (Facultative) TRUE to throw exception on check fail, FALSE or default to return false.
     * @param Integer $timespan (Facultative) Makes the token expire after $timespan seconds. (null = never)
     * @param Boolean $multiple (Facultative) Makes the token reusable and not one-time. (Useful for ajax-heavy requests).
     * 
     * @return Boolean Returns FALSE if a CSRF attack is detected, TRUE otherwise.
     */
    public function check($key, $origin, $throwException = false, $timespan = null, $multiple = false)
    {
        // REMOVED if ( !isset( $_SESSION[ 'csrf_' . $key ] ) )
        
        if (!$this->session->has('csrf_' . $key)){
            if ($throwException){
                throw new Exception('Missing CSRF session token.');
            }else{
                return false;
            }
        }    
            
        if (!isset($origin[$key])){
            if ($throwException){
                throw new Exception('Missing CSRF form token.');
            }else{
                return false;
            }
        }
        // Get valid token from session
        // REMOVED $hash = $_SESSION[ 'csrf_' . $key ];
        $hash = $this->session->get('csrf_' . $key);
        // Free up session token for one-time CSRF token usage.
        
        if (!$multiple){
            $this->session->set('csrf_' . $key, null);
        }
        // REMOVED $_SESSION[ 'csrf_' . $key ] = null;
        // Origin checks
        
        if ($this->doOriginCheck && sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']) != substr(base64_decode($hash), 10, 40)) {
            
            if ($throwException){
                throw new Exception('Form origin does not match token origin.');
            }else{
                return false;
            }
        }
        
        // Check if session token matches form token
        
        if ($origin[$key] != $hash){
            if ($throwException){
                throw new Exception('Invalid CSRF token.');
            }else{
                return false;
            }
        }
        
        // Check for token expiration
        if ($timespan != null && is_int($timespan) && intval(substr(base64_decode($hash), 0, 10)) + $timespan < time()){
            if ($throwException){
                throw new Exception('CSRF token has expired.');
            }else{
                return false;
            }
        }
        return true;
    }
    
    /**
     * Adds extra useragent and remote_addr checks to CSRF protections.
     */
    public function enableOriginCheck()
    {
        $this->doOriginCheck = true;
    }
    
    /**
     * CSRF token generation method. After generating the token, put it inside a hidden form field named $key.
     *
     * @param String $key The session key where the token will be stored. (Will also be the name of the hidden field name)
     * @return String The generated, base64 encoded token.
     */
    public function generate($key)
    {
        if($this->doOriginCheck){
            $extra = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        }else{
            $extra = '';
        }
        //$extra = $this->doOriginCheck ? sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']) : '';
        // token generation (basically base64_encode any random complex string, time() is used for token expiration) 
        $token = base64_encode(time() . $extra . $this->randomString(32));
        // store the one-time token in session
        $this->session->set('csrf_' . $key, $token);
        // REMOVED $_SESSION[ 'csrf_' . $key ] = $token;
        return $token;
    }
    
    /**
     * Generates a random string of given $length.
     *
     * @param Integer $length The string length.
     * @return String The randomly generated string.
     */
    protected function randomString($length)
    {
        $seed   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max    = strlen($seed) - 1;
        $string = '';
        for ($i = 0; $i < $length; ++$i){
            $string .= $seed{intval(mt_rand(0.0, $max))};
        }
        return $string;
    }
    
}