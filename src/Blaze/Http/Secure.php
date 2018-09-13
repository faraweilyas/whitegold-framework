<?php 
	namespace Blaze\Http;

    /**
    * whiteGold - mini PHP Framework
    *
    * @package whiteGold
    * @author Farawe iLyas <faraweilyas@gmail.com>
    * @link http://faraweilyas.me
    *
    * Secure Class
    */
	class Secure extends SessionHelper
	{
        /**
        * Configures and starts the session.
        */
        public function __construct () 
        {
            $this->initialize();
        }

		/**
		* Create csrf token in an html input tag.
		*/
		public function csrfTokenTag ()
		{
			$token = $this->createCsrfToken();
			print "<input type='hidden' name='csrfToken' value='$token' />".PHP_EOL;
		}

		/**
		* Check the token validity.
		* @return bool
		*/
		public function checkCrfToken () : bool
		{
			if (!$this->isCsrfTokenValid() OR !$this->isCsrfTokenRecent())
			{			
				$this->destroyCsrfToken();
				return FALSE;
			}
			return TRUE;
		}

		/**
		* Validate request.
		* @param string $requestType
		* @param bool $isStrict
		* @return bool
		*/
		public function checkRequestType (string $requestType, bool $isStrict=TRUE) : bool
		{
			if (!$this->requestType($requestType)) return FALSE;
			if ($isStrict):
				if (!$this->requestIsSameDomain()) return FALSE;
			endif;
			return TRUE;
		}

		/**
		* Generate a token for use with CSRF protection.
		* @return string
		*/
		private function createCsrfToken () : string
		{
			$token 							= md5(uniqid(rand(), TRUE));
			$_SESSION['csrfToken'] 			= $token;
			$_SESSION['csrfTokenTime'] 		= time();
			return $token;
		}

		/**
		* Check if csrf token is valid.
		* @return bool
		*/
		private function isCsrfTokenValid () : bool
		{
			if (isset($_POST['csrfToken']) && isset($_SESSION['csrfToken']))
				return $_POST['csrfToken'] === $_SESSION['csrfToken'];
			else
				return FALSE;
		}

		/**
		* Check if csrf token is recent
		* @return bool
		*/
		private function isCsrfTokenRecent () : bool
		{
			$maxElapsed = 60 * 60;
			if (!isset($_SESSION['csrfTokenTime'])) return FALSE;
			return time() < ($_SESSION['csrfTokenTime'] + $maxElapsed);
		}

		/**
		* Check request type.
		* @param string $requestType
		* @return bool
		*/
		private function requestType (string $requestType) : bool
		{
			return $_SERVER['REQUEST_METHOD'] === strtoupper($requestType);
		}

		/**
		* Check if request host matches server host.
		* @return bool
		*/
		private function requestIsSameDomain () : bool
		{
			if (!isset($_SERVER['HTTP_REFERER'])) return FALSE;
			$refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
			return ($refererHost == $_SERVER['HTTP_HOST']) ? TRUE : FALSE;
		}

		/**
		* Destroy csrf token.
		*/
		private function destroyCsrfToken ()
		{
			if (isset($_SESSION['csrfToken'])) 		$_SESSION['csrfToken'] 		= NULL;
			if (isset($_SESSION['csrfTokenTime'])) 	$_SESSION['csrfTokenTime'] 	= NULL;
		}
	}