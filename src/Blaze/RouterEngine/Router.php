<?php 
	namespace Blaze\RouterEngine;
	
	use Blaze\Exception\ErrorCode;
	use Blaze\Validation\Validator as Validate;
	
	/**
	* whiteGold - mini PHP Framework
	*
	* @package whiteGold
	* @author Farawe iLyas <faraweilyas@gmail.com>
	* @link http://faraweilyas.me
	*
	* Router Class
	*/
	class Router extends RouterParts
	{
		/**
		* URI route parameter.
		* @var string
		*/
		public static $route = "";

		/**
		* Registered named routes.
		* @var array
		*/
		public static $namedRoutes = [];

		/**
		* Router initializer
		*/
		final public static function initialize ()
		{
			static::getUrl();
	        static::checkAppState();
	        static::validateRequestedRoute();
		}

		/**
		* Returns current requested route.
		* @return string
		*/
		final public static function getRequestedRoute () : string
		{
			return (php_sapi_name() === 'cli-server')
					? urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
					: "/".($_GET['url'] ?? "");
		}

		/**
		* It gets the get url parameter passed from the RewriteEngine in HTACCESS file.
		*/
		final protected static function getUrl ()
		{
			$requestUri 	= static::getRequestedRoute();
			static::$route 	= $url = (isset($requestUri) AND !empty($requestUri)) ? $requestUri : '/';
			$length 		= strlen($url);
			$dirSlash 		= substr($url, $length - 1);
			if ($dirSlash == "/" AND $url != "/") $url = substr($url, 0, $length-1);
			static::$_getRoute 	= $url;
			static::$_pattern 	= "#^$url$#";
		}

		/**
		* It registers the route and method to the class properties for evaluation.
		* @param string $route
		* @param mixed $method
		* @param string $routName
		*/
		final public static function register (string $route, $method, string $routName=NULL)
		{
			$route 							= '/'.trim($route, '/');
			static::$_uri[] 				= $route;
			static::$_methods[] 			= $method;
			$routName 						= !empty($routName) ? $routName : "";
			static::$namedRoutes[$route] 	= $routName;
		}
		
		/**
		* Checks for routes that matches the requested route.
		*/
		final protected static function validateRequestedRoute ()
		{
			if (static::routeMatch()):
				$matched_route 	= static::$_matchedRoute['route'];
				$method 		= static::$_methods[static::$_matchedRoute['method_key']];
				if (!static::caller($method)):
					echo "Caller Error";
				endif;
			else:
				if (!file_exists(getConstant('VIEW', TRUE).'AppStates/404.php')):
					$message  = "404 Error Found: cause the requested page wasn't found. <br />";
					$message .= "If you're the admin you can specify your 404 Error file in your ";
					$message .= "'".getConstant('VIEW', TRUE)."AppStates/' directory and name it '404.php'";
					print $message; exit;
				endif;
				RouterView::make("AppStates.404");
			endif;
		}

		/**
		* Checks the app state before routing.
		*/
		final protected static function checkAppState ()
		{
			if (!getConstant('UNDER_CONSTRUCTION')) return;
			if (!file_exists(getConstant('VIEW', TRUE).'AppStates/UC.php')):
				$message  = "The APP is in maintenance mode. <br />If you're the admin you can specify your maintenance file in your ";
				$message .= "'".getConstant('VIEW', TRUE)."AppStates/' directory and name it 'UC.php'";
				print $message; exit;
			endif;
			RouterView::make('AppStates.UC');
			exit;			
		}

		/**
		* It calls the route helper for proper file inclusion for HTML.
		* @param string $file
		* @param bool $return
		* @return string
		*/
		final public static function _file (string $file, bool $return=TRUE) : string
		{
			return static::routeDriver($file, $return);
		}

		/**
		* It calls the route helper for proper url linking for HTML.
		* @param string $url
		* @param bool $return
		* @return string
		*/
		final public static function _url (string $url, bool $return=TRUE) : string
		{
			return static::routeDriver($url, $return);
		}

		/**
		* It calls the route helper for proper redirection.
		* @param string $route
		* @param bool $return
		* @return string
		*/
		final public static function redirectRoute (string $route, bool $return=TRUE) : string
		{
			return static::routeDriver($route, $return);
		}

		/**
		* Returns current route.
		* @return string
		*/
		final public static function getRoute () : string
		{
			return static::$route;
		}

		/**
		* Returns registered routes.
		* @return array
		*/
		final public static function getRoutes () : array
		{
			return static::$_uri;
		}

		/**
		* Returns registered named routes.
		* @return array
		*/
		final public static function getNamedRoutes () : array
		{
			return static::$namedRoutes;
		}
	}