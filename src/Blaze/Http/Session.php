<?php
	namespace Blaze\Http;

    /**
    * whiteGold - mini PHP Framework
    *
    * @package whiteGold
    * @author Farawe iLyas <faraweilyas@gmail.com>
    * @link http://faraweilyas.me
    *
    * Session Class
    */
    class Session extends SessionHelper
    {
        /**
        * Stores session id
        * @var int
        */
        protected $id;
        /**
        * Stores maximum elapsed time
        * @var int
        */
        protected $maxElapsed;
        /**
        * Stores user logged in state
        * @var bool
        */
        protected $loggedIn     = FALSE;
        /**
        * Determine if IP is to be checked or not
        * @var bool
        */
        public $checkIP         = FALSE;
        /**
        * Determine if user agent is to be checked or not
        * @var bool
        */
        public $checkAgent      = TRUE;
        /**
        * Determine if last login is to be checked or not
        * @var bool
        */
        public $checkLastLogin  = TRUE;
        
        /**
        * Configures and starts the session.
        */
        public function __construct () 
        {
            $this->cofigureSession("domain", getConstant("MASTER_DOMAIN"));
            $this->initialize();
            $this->checkLogin();
            $this->setMaxElapsed($this->getMaxElapsed());
        }

        /**
        * Sets maximum elapsed time.
        * @param int $maxElapsed 
        */
        public function getMaxElapsed () : string
        {
            // if (!$this->isSessionValid()) Session::setSession("MAX_ELAPSED", NULL);
            if (Session::getSession("REMEMBER_ME") == 'yes'):
                // 1 week
                $maxElapsed = Session::getSession("MAX_ELAPSED") ?? (60 * 60 * 24 * 7);
            else:
                // 1 Day
                $maxElapsed = Session::getSession("MAX_ELAPSED") ?? (60 * 60 * 24);
            endif;
            return (string) $maxElapsed;
        }

        /**
        * Sets maximum elapsed time.
        * @param int $maxElapsed 
        */
        public function setMaxElapsed (int $maxElapsed)
        {
            $this->maxElapsed = $maxElapsed;
        }

        /**
        * Get ID.
        * @return int
        */
        public function getID () : int
        {
            return $this->id ?? 0;
        }

        /**
        * Log user in.
        * @param int $id
        */
        public function login (int $id)
        {
            $this->id               = $_SESSION['ID'] = $id;
            $this->loggedIn         = TRUE;
            $_SESSION['loggedIn']   = TRUE;
            $_SESSION['IP']         = getUserIP();
            $_SESSION['userAgent']  = getNavigator();
            $_SESSION['lastLogin']  = time();
            static::newSessionID();
        }

        /**
        * Logs user out of thier session
        */
        public function logout ()
        {
            unset($_SESSION['ID']);
            unset($this->id);
            $this->loggedIn         = FALSE;
            $_SESSION['loggedIn']   = FALSE;
            Session::setSession("REMEMBER_ME", NULL);
            Session::setSession("MAX_ELAPSED", NULL);
            static::endSession();
        }

        /**
        * Checks user session on new instantiation and populate user session object
        */
        protected function checkLogin ()
        {
            if (isset($_SESSION['ID'])) {
                $this->id           = $_SESSION['ID'];
                $this->loggedIn     = TRUE;
            } else {
                $this->id           = NULL;
                $this->loggedIn     = FALSE;
            }
        }

        /**
        * Checks if user is logged in.
        * @return bool
        */
        public function isLoggedIn () : bool
        {
            return $this->loggedIn;
        }

        /**
        * Restrict access to a page if session is not valid.
        * @param string $location
        */
        public function lockPage (string $location)
        {
            if (!$this->isUserLoggedIn()) redirectTo($location);
        }

        /**
        * Checks if user is logged in and if session is valid.
        * @return bool
        */
        public function isUserLoggedIn () : bool
        {
            if (!$this->isLoggedIn() || !$this->isSessionValid()) return FALSE;
            return TRUE;
        }

        /**
        * Restrict access to a page and end session if session is not valid.
        * @param string $location
        */
        public function protectedPage (string $location)
        {
            $this->confirmUserIsValid($location);
        }

        /**
        * Confirm that the user is logged in and session is not valid.
        * @param string $location
        */
        public function confirmUserIsValid (string $location)
        {
            if (!$this->isLoggedIn() || !$this->isSessionValid())
            {
                static::endSession();
                redirectTo($location);                
            }
        }

        /**
        * Check if session is valid.
        * @return bool
        */
        public function isSessionValid () : bool
        {

            $checkIPMatchStored = $this->checkIPMatchStored();
            if ($this->checkIP AND !$checkIPMatchStored) return FALSE;

            $checkUserAgentMatchesStored = $this->checkUserAgentMatchesStored();
            if ($this->checkAgent AND !$checkUserAgentMatchesStored) return FALSE;

            $lastLoginIsRecent = $this->lastLoginIsRecent();
            if ($this->checkLastLogin AND !$lastLoginIsRecent) return FALSE;

            return TRUE;
        }

        /**
        * Check if requested IP matches stored IP.
        * @return bool
        */
        protected function checkIPMatchStored () : bool
        {
            if (!isset($_SESSION['IP']) || empty(getUserIP())) 
                return FALSE;
            if ($_SESSION['IP'] !== getUserIP()) return FALSE;
            return TRUE;
        }

        /**
        * Check if requested user agent matches stored user agent.
        * @return bool
        */
        protected function checkUserAgentMatchesStored () : bool
        {
            if (!isset($_SESSION['userAgent']) || empty(getNavigator())) 
                return FALSE;
            if ($_SESSION['userAgent'] !== getNavigator()) return FALSE;
            return TRUE;
        }

        /**
        * Has too much time passed since the last login.
        * @return bool
        */
        protected function lastLoginIsRecent () : bool
        {
            if (!isset($_SESSION['lastLogin'])) return FALSE;
            if (($_SESSION['lastLogin'] + $this->maxElapsed) < time()) 
                return FALSE;
            return TRUE;
        }
    }