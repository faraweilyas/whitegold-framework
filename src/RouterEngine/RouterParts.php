<?php

namespace Blaze\RouterEngine;

use Blaze\Http\UrlParser;
use Blaze\Exception\ErrorCode;
use Blaze\Validation\Validator as Validate;
use Blaze\RouterEngine\RouterDaemonInterface;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link https://faraweilyas.com
*
* RouterParts Class
*/
abstract class RouterParts implements RouterDaemonInterface
{
	/**
	* Stores the uri's.
	* @var array
	*/
	protected static $_uri 				= [];

	/**
	* Stores the uris methods.
	* @var array
	*/
	protected static $_methods 			= [];

	/**
	* Stores the arguments passed in the callable method.
	* @var array
	*/
	protected static $_arguments 		= [];

	/**
	* Matched Routes.
	* @var array
	*/
	protected static $_matchedRoute 	= [];

	/**
	* GET route parameter for evaluation.
	* @var string
	*/
	protected static $_getRoute 		= "";

	/**
	* Regex pattern for route.
	* @var string
	*/
	protected static $_pattern 			= "";

	/**
	* Absolute route.
	* @var bool
	*/
	public static $absoluteRoute 		= FALSE;

	/**
	* Checks if requested route matches any of the registered routes {Match Algorithm}.
	* @return bool
	*/
	final protected static function routeMatch () : bool
	{
		if (static::routePatternMatch()) return TRUE;

		$definedRoute 	= static::defineRequestedRoute();
        $definedRoutes 	= static::defineRoutes(static::routeLengthMatch());
		$matchedRoutes 	= static::searchUris($definedRoute, $definedRoutes);

        if (count($matchedRoutes) > 1)
        	$matchedRoutes = static::checkMatchedRoutes($matchedRoutes);

		static::setVariables();
		if (!empty($matchedRoutes)):
			static::$_matchedRoute = [
				"route" 		=> $matchedRoutes[key($matchedRoutes)], 
				"method_key" 	=> key($matchedRoutes)
			];
		endif;
		return (!empty($matchedRoutes)) ? TRUE : FALSE;
	}

	/**
	* Checks for any route matching the defined routes with preg match {Regex}.
	* @return bool
	*/
	final protected static function routePatternMatch () : bool
	{
		$matchedRoute = ''; $methodKey = '';		
		foreach (static::$_uri as $key => $route):
			if (@preg_match(static::$_pattern, $route)):
				$matchedRoute 	= $route;
				$methodKey 		= $key;
			endif;
		endforeach;
		if (Validate::hasValue($matchedRoute, $methodKey)):
			static::$_matchedRoute = [
				"route" 		=> $matchedRoute,
				"method_key" 	=> $methodKey
			];
			return TRUE;
		endif;
		return FALSE;
	}

	/**
	* Defines requested route for matching.
	* @return string
	*/
	final protected static function defineRequestedRoute () : string
	{
		$requestedRouteOrder 	= "";
		$requestedRouteUris 	= static::uriGenerator(static::$_getRoute);
		$keyRoutes 				= static::explodeRoutes();
		foreach ($requestedRouteUris as $requestedUri):
			if (Validate::hasValue($requestedUri)):
				if (in_array($requestedUri, $keyRoutes)) 
					$requestedRouteOrder .= "Route ";
				elseif ($requestedUri == "/") 
					$requestedRouteOrder .= "";
				else
					$requestedRouteOrder .= "Variable ";
			endif;
		endforeach;
		return trim($requestedRouteOrder);
	}
	
	/**
	* Checks if requested route matches with any of the defined routes {Equal Length}.
	* @return array
	*/
	final protected static function routeLengthMatch () : array
	{
		$matchedRoutes 		= [];	
		$requestedUris 		= array_filter(explode("/", static::$_getRoute));
		foreach (static::$_uri as $key => $uri):
			$registeredUris = array_filter(explode("/", $uri));
			if (count($registeredUris) == count($requestedUris))
				if (!in_array($uri, $matchedRoutes)) $matchedRoutes[$key] = $uri;
		endforeach;
		return $matchedRoutes;
	}

	/**
	* Define Registered Routes for Matching.
	* @param array $routes
	* @return array
	*/
	final protected static function defineRoutes (array $routes=[]) : array
	{
		$definedRouteOrders = [];
		if (empty($routes)) $routes = static::$_uri;
		foreach ($routes as $key => $_uri) $definedRouteOrders[$key] = self::defineRoute($_uri);
		return $definedRouteOrders;
	}

	/**
	* Defines route for matching.
	* @param string $route
	* @return string
	*/
	final protected static function defineRoute (string $route) : string
	{
		$routeOrder = "";
		$routeUris 	= static::uriGenerator($route);
		foreach ($routeUris as $uri):
			if (Validate::hasValue($uri)):
				if ((stripos($uri, "{") !== FALSE) AND (stripos($uri, "}") !== FALSE))
					$routeOrder .= "Variable ";
				else
					$routeOrder .= "Route ";
			endif;
		endforeach;
		return trim($routeOrder);
	}

	/**
	* Return matched route from suggested matched routes.
	* @param array $routes
	* @return array
	*/
	final protected static function checkMatchedRoutes (array $routes=[]) : array
	{
		$finalMatch 		= [];
		if (empty($routes)) return $finalMatch;
        $getRoute 			= static::uriCommaAnalyzer($routes);
		$definedRoutes		= static::defineRouteVariable($routes);

    	foreach ($definedRoutes as $key => $route):
			if (@preg_match("#^$getRoute$#", $route)):
				if (!in_array($routes[$key], $finalMatch))
					$finalMatch[$key] = $routes[$key];
			endif;
    	endforeach;
    	return $finalMatch;
	}

	/**
	* Analyzes string of uris with commas from routes.
	* @param array $routes
	* @return string
	*/
	final protected static function uriCommaAnalyzer (array $routes) : string
	{
		$uris 		= array_filter(explode("/", static::$_getRoute));
		$keyRoutes 	= static::explodeRoutes($routes);
		$getRoute 	= "";
        foreach ($uris as $uri):
			if (Validate::hasValue($uri))
				if (in_array($uri, $keyRoutes)) $getRoute .= "$uri,";
        endforeach;
        return substr($getRoute, 0, strlen($getRoute) - 1);
	}

	/**
	* Defines suggested routes into strings for pattern matching.
	* @param array $routes
	* @return array
	*/
	final protected static function defineRouteVariable (array $routes) : array
	{
		$definedRoutes = [];
        foreach ($routes as $key => $route):
        	$route 			= array_filter(explode("/", $route)); 
        	$matchedRoutes 	= [];
        	foreach ($route as $uri):
				if (Validate::hasValue($uri)):
					if ((stripos($uri, "{") == FALSE) AND (stripos($uri, "}") == FALSE))
						$matchedRoutes[] = $uri;
				endif;
	        endforeach;
        	$definedRoutes[$key] = implode(",", $matchedRoutes);
        endforeach;
        return $definedRoutes;
	}

	/**
	* Sets variables from requested route for argument parsing.
	* @return void
	*/
	final protected static function setVariables ()
	{
		$variables 		= [];
		$definedRoute 	= static::defineRequestedRoute();
		$defined  		= array_filter(explode(" ", $definedRoute));
		$route 			= substr(static::$_getRoute, 1);
		$uris 			= (stripos($route, "/") != FALSE) ? array_filter(explode("/", $route)) : [$route];
		foreach ($uris as $key => $uri):
			if (Validate::hasValue($uri))
				if ($defined[$key] != "Route") $variables[] = $uri;
		endforeach;
		static::$_arguments = $variables;
	}

	/**
	* Finds appropraite method to call from the registered methods.
	* @param mixed $method
	* @return bool
	*/
	final protected static function caller ($method) : bool
	{
		if (empty($method)) return FALSE;

		if (is_callable($method)):
			static::callClosure($method); 
			return TRUE;
		endif;
		if (is_string($method)):
			static::callWorker($method); 
			return TRUE;
		endif;
		return FALSE;
	}

	/**
	* Calls annonymous functions sepcified in the route.
	* @param callable $method
	* @return void
	*/
	final protected static function callClosure (callable $method=NULL)
	{
		$fail 						= FALSE;
		$info 						= new \ReflectionFunction($method);
		$callableArgumentCount 		= $info->getNumberOfParameters();
		$requiredArgumentCount 		= $info->getNumberOfRequiredParameters();

		if (empty(static::$_arguments)) $fail = TRUE;

		if ($callableArgumentCount > 0 AND $fail) new ErrorCode(1004);

		if ($callableArgumentCount > 1 AND !$fail)
			call_user_func_array($method, static::$_arguments);
		elseif ($callableArgumentCount == 1 AND !$fail)
			call_user_func($method, static::$_arguments[0]);
		else
			call_user_func($method);
	}

	/**
	* Calls class and method sepcified for the requested route.
	* @param string $classMethod
	* @return void
	*/
	final protected static function callWorker (string $classMethod=NULL)
	{
		$method 	= ''; 
		$object 	= '';
		if (substr_count($classMethod, '@') == 1) 
			list($class, $method) = array_filter(explode("@", $classMethod));

		if (class_exists($class)):
			$class 		= ucfirst($class);
			$object 	= new $class;
		endif;

		if (!empty($method) AND method_exists($object, $method)):
			if (count(static::$_arguments) > 1)
				call_user_func_array([$object, $method], static::$_arguments);
			elseif (count(static::$_arguments) == 1)
				call_user_func([$object, $method], static::$_arguments[0]);
			else
				call_user_func([$object, $method]);
		else:
			new ErrorCode(1003);
		endif;
	}

	/**
	* Generates array of uris from routes.
	* @param string $route
	* @return array
	*/
	final public static function uriGenerator (string $route) : array
	{
		return ($route == "/") ? ["/"] : array_filter(explode("/", $route));
	}

	/**
	* Generates array of uris from searched defined routes.
	* @param string $needle
	* @param array $haystack
	* @return array
	*/
	final public static function searchUris (string $needle, array $haystack) : array
	{
		$result = [];
		foreach ($haystack as $key => $value)
        	if ($needle == $value) $result[$key] = static::$_uri[$key];
        return $result;
	}

	/**
	* Generates routes for searching {Explosion}.
	* @param array $routes
	* @return array
	*/
	final protected static function explodeRoutes (array $routes=[]) : array
	{
		$explodedRoutes = [];
		if (empty($routes)) $routes = static::$_uri;
		foreach ($routes as $_uri):
			$uris = static::uriGenerator($_uri);
			foreach ($uris as $uri)
				if (Validate::hasValue($uri) AND !in_array($uri, $explodedRoutes)) $explodedRoutes[] = $uri;
		endforeach;
		return $explodedRoutes;
	}
	
	/**
	* Activate absolute route.
	* @return bool
	*/
	final public static function activateAbsoluteRoute() : bool
	{
		static::$absoluteRoute = TRUE;
		return static::$absoluteRoute;
	}
	
	/**
	* Activate relative route.
	* @return bool
	*/
	final public static function activateRelativeRoute() : bool
	{
		static::$absoluteRoute = FALSE;
		return static::$absoluteRoute;
	}
	
	/**
	 * It gets the raw GET url parameter for proper evaluation against route.
	 * @param string $route
	 * @return string
	 */
	final protected static function routeHelper(string $route) : string
	{
		$urlParser 		= new UrlParser($route);
		if ($urlParser->isThereScheme()) return $route;
		static::$route 	= empty(static::$route) ? static::getRequestedRoute() : static::$route;
		$occurences 	= substr_count(static::$route, "/");
		$route 			= "./".ltrim($route, "./");
		if (static::$absoluteRoute AND php_sapi_name() != "cli"):
			return getHost()."/".ltrim($route, "./");
		endif;
		if ($occurences > 1 AND Validate::hasValue($route)):
			$route 		= ltrim($route, "./");
			$route 		= str_repeat("../", ($occurences - 1)).$route;
			return $route;
		endif;
		return $route;
	}
	
	/**
	* Checks route for analyzing for FILE, URL, REDIRECTION in HTML.
	* @param string $value
	* @param bool $return
	* @return string
	*/
	final protected static function routeDriver (string $value, bool $return=TRUE) : string
	{
		if (!$return):
			print static::routeHelper($value);
		endif;
		return static::routeHelper($value);
	}

	/**
	* Debugger that shows steps on how router works for developers.
	* @return void
	*/
	public static function _DEBUG ()
	{
		echo "<h4>Step 1: Registered Routes</h4>";
		echo '<tt><pre>'.var_export(static::$_uri, TRUE).'</pre></tt>';

		echo "<h4>Step 2: Registered Methods</h4>";
		echo '<tt><pre>'.var_export(static::$_methods, TRUE).'</pre></tt>';

		echo "<h4>Step 3: Requested Get Route</h4>";
		static::getUrl();
		echo '<tt><pre>'.var_export(static::$_getRoute, TRUE).'</pre></tt>';

		echo "<h4>Step 4: Define the Requested Get Route</h4>";
		echo '<tt><pre>'.var_export(static::defineRequestedRoute(), TRUE).'</pre></tt>';

		echo "<h4>Step 5: Routes That Matches the Length of Requested Route</h4>";
		echo '<tt><pre>'.var_export(static::routeLengthMatch(), TRUE).'</pre></tt>';

		echo "<h4>Step 6: {Defines Routes} Uses Result From Routes That Matches the Length of Requested Route</h4>";
		echo '<tt><pre>'.var_export(static::defineRoutes(static::routeLengthMatch()), TRUE).'</pre></tt>';

		echo "<h4>Step 7: Search for a match with the defined route from the defined registered routes</h4>";
		$matchedRoutes 	= static::searchUris
		(
			static::defineRequestedRoute(), 
			static::defineRoutes(static::routeLengthMatch())
		);
		echo '<tt><pre>'.var_export($matchedRoutes, TRUE).'</pre></tt>';

		echo "<h4>Step 8: Analyze requested route</h4>";
		echo '<tt><pre>'.var_export(static::uriCommaAnalyzer($matchedRoutes), TRUE).'</pre></tt>';

		echo "<h4>Step 9: Analyze registered routes</h4>";
		echo '<tt><pre>'.var_export(static::defineRouteVariable($matchedRoutes), TRUE).'</pre></tt>';

		echo "<h4>Step 10: Check for routes that match the requested route</h4>";
		echo '<tt><pre>'.var_export(static::checkMatchedRoutes($matchedRoutes), TRUE).'</pre></tt>';

		echo "<h4>Step 11: Arguments for requested route method</h4>";
		static::setVariables();
		echo '<tt><pre>'.var_export(static::$_arguments, TRUE).'</pre></tt>';

		echo "<h4>Step 12: Call method that belongs to the route</h4>";
		echo '<tt><pre>'.var_export(static::caller(
			static::$_methods[static::$_matchedRoute['method_key']]
		), TRUE).'</pre></tt>';

		echo "<h4>Final Step: Found Route</h4>";
		echo '<tt><pre>'.var_export(static::$_matchedRoute, TRUE).'</pre></tt>';
	}
}
