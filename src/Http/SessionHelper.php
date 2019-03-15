<?php

namespace Blaze\Http;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link http://faraweilyas.me
*
* SessionHelper Class
* Useful php.ini file settings:
* session.cookie_lifetime = 0
* session.cookie_secure = 1
* session.cookie_httponly = 1
* session.use_only_cookies = 1
* session.entropy_file = "/dev/urandom"
*/
abstract class SessionHelper
{
    protected $expire;
    protected $path;
    protected $domain;
    protected $secure;
    protected $httponly;

    /**
    * Initialize the session.
    * @return void
    */
    protected function initialize ()
    {
        $this->startSession();
    }

    /**
    * Starts session.
    */
    protected function startSession ()
    {
        if (session_status() == PHP_SESSION_NONE):
	        $this->setSessionConfig();
        	session_start();
        endif;
    }

    /**
    * Sets session cofiguration before session starts.
    * @return void
    */
    protected function setSessionConfig ()
    {
        // 1 WEEK FROM NOW
        $oneWeek        = time() + 60 * 60 * 24 * 7;
        $this->expire   = $this->expire     ?? $oneWeek;
        // '/path' or dir that is using cookies
        $this->path     = $this->path       ?? "/";
        // 'www.mysite.com';
        $this->domain   = $this->domain     ?? NULL;
        // isset($_SERVER['HTTPS']);
        $this->secure   = $this->secure     ?? NULL;
        // JavaScript can't access cookie
        $this->httponly = $this->httponly   ?? TRUE;
        session_set_cookie_params($this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    /**
    * Configures session.
    * @param string $name 
    * @param mixed $value 
    * @return bool
    */
    public function cofigureSession (string $name, $value) : bool
    {
        if (!property_exists($this, $name)) return FALSE;
        $allowedProperties = ['expire', 'path', 'domain', 'secure', 'httponly'];
        if (!in_array(strtolower($name), $allowedProperties)) return FALSE;
        $this->$name = $value;                
        return TRUE;
    }

    /**
    * Set a session.
    * @param string $sessionName
    * @param mixed $sessionValue
    */
    public static function setSession (string $sessionName, $sessionValue)
    {
        $_SESSION[$sessionName] = $sessionValue;
    }

    /**
    * Get a session.
    * @param string $sessionName
    * @return mixed
    */
    public static function getSession (string $sessionName)
    {
        return $_SESSION[$sessionName] ?? NULL;
    }

    /**
    * Gets session configuration
    * @return void
    */
    public static function getSessionParams ()
    {
        $sessionInfo = session_get_cookie_params();
        print "<br />";
        foreach ($sessionInfo as $key => $value)
            print "$key: $value <br />";
    }

    /**
    * Regenerates new session id.
    * @return void
    */
    final public static function newSessionID ()
    {
        session_regenerate_id();
    }

    /**
    * Forcibly end the session.
    * @return void
    */
    final public static function endSession ()
    {
        // Use both for compatibility with all browsers and all versions of PHP.
        session_unset(); session_destroy();
    }
}
