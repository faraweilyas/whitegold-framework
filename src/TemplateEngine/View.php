<?php

namespace Blaze\TemplateEngine;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * View Class
 */
class View
{
	// Stores the location of the views that are being included.
	public static $location;

	// Stores the variables that are passed to the view.
	public static $passedVariables = [];

	/**
	 * Get the view location
	 * @return string
	 */
	final public static function getLocation() : string
	{
    	return empty(static::$location) ? getConstant("VIEW") : static::$location;
	}

	/**
	 * Make a view.
	 * @param string $file
	 * @param array $variables
	 * @return bool
	 */
	public static function make(string $file, array $variables=[]) : bool
	{
		$fileLocation = static::fileAnalyzer($file);			
		if (file_exists($fileLocation)):
			ob_start(getConstant('MINIFY_HTML_OUTPUT') ? 'minifyHTMLOutput' : NULL);
			if (!empty($variables)) self::with($variables);
	        extract($GLOBALS, EXTR_OVERWRITE);
			extract(static::$passedVariables, EXTR_OVERWRITE);
			require getConstant("BOOTSTRAP", TRUE)."Initializers.php";
			require_once $fileLocation;
			ob_end_flush();

			// ob_start();
			// debug_print_backtrace();
			// $trace = ob_get_contents();
			// ob_end_clean();
			return TRUE;
		endif;
		print "'$fileLocation': File was not found."; 
		return FALSE;
	}

	/**
	 * Set the class property passedVariables to pass variables to the view.
	 * @param array $variables
	 * @return object
	 */
	final public static function with(array $variables) : View
	{
		foreach ($variables as $variableName => $variableValue) 
			static::$passedVariables[$variableName] = $variableValue;
		return new View;
	}

	/**
	 * Analyzes file for inclusion.
	 * @param string $file
	 * @return string
	 */
	final protected static function fileAnalyzer(string $file) : string
	{
		return static::getLocation().str_replace(".", "/", $file).".php";
	}
}
