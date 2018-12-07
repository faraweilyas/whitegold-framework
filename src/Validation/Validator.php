<?php

namespace Blaze\Validation;

use Blaze\Exception\ErrorCode;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link http://faraweilyas.me
*
* Validator Class
*/
abstract class Validator
{
	public static $error 	= "";
	public static $errors 	= [];

	// Specific regular expression formats
	// (Use \A and \Z, not ^ and $ which allow line returns.)
    const FORMAT_NAME 		= "/\A([A-Za-z.'\-]+) ?(?:([A-Za-z.'\-]+) )?([A-Za-z.'\-]+)\Z/";
    const FORMAT_USERNAME 	= "/\A(?=.*[~!@#$%^&*()_\-+=|\\{}[\]:;<>?\/])(?=.*[A-Za-z0-9])\S{6,15}\Z/";
    const FORMAT_EMAIL 		= "/\A[\w.%+\-]+@[\w.\-]+\.[A-Za-z]{2,6}\Z/";
    const FORMAT_PASSWORD 	= "/\A(?=.*\d)(?=.*[~!@#$%^&*()_\-+=|\\{}[\]:;<>?\/]?)(?=.*[A-Z])(?=.*[a-z])\S{5,15}\Z/";
    const FORMAT_URL 		= "/\A(?:http|https):\/\/[\w.\-]+(?:\.[\w\-]+)+[\w\-.,@?^=%&:;\/~\\+#]+\Z/";

	/**
	* Checks if an attribute has a value.
    * @param mixed $values
    * @return bool
	*/
	final public static function hasValue (...$values) : bool
	{
		foreach ($values as $value):
			if (is_array($value)):
				if (!(isset($value) AND (!empty($value)))):
					return FALSE; break;
				endif;
			else:
				$trimmedValue = trim($value);
				if (!(isset($trimmedValue) AND ($trimmedValue !== ""))):
					return FALSE; break;
				endif;
			endif;
		endforeach;
		return TRUE;
	}

	/**
	* Validates if first parameter is less than, equal to or greater than second parameter.
    * @param mixed $firstValue
    * @param mixed $secondValue 
    * @return int
	*/
	final public static function compareValues ($firstValue, $secondValue) : int
	{
		if (!self::hasValue($firstValue, $secondValue)) new ErrorCode(1001);
	    return $firstValue <=> $secondValue;
	}

	/**
	* Checks for the maximum, minimum or exact length of a value.
    * @param mixed $value
    * @param array $options 
    * @return bool
	*/
	final public static function hasLength ($value, array $options=[]) : bool
	{
		$valueLength = strlen($value);
		if (!self::hasValue($value)) new ErrorCode(1001);

		if (isset($options['max']) && ($valueLength > (int) $options['max']))
			return FALSE;

		if (isset($options['min']) && ($valueLength < (int) $options['min']))
			return FALSE;

		if (isset($options['exact']) && ($valueLength != (int) $options['exact']))
			return FALSE;

		return TRUE;
	}

	/**
	* Validates if parameter is a number and if options aren't empty it checks the length.
    * @param mixed $value
    * @param array $options 
    * @return bool
	*/
	final public static function hasNumber ($value, array $options=[]) : bool
	{
		if (!self::hasValue($value))  new ErrorCode(1001);

		// Submitted values are strings, so use is_numeric instead of is_int
		if (!is_numeric($value)) return FALSE;

		if (!empty($options)) return static::hasLength($value, $options);

		return TRUE;
	}

	/**
	* Checks if parameter matches a certain format.
    * @param mixed $value
    * @param regex $format 
    * @return bool
	*/
	final public static function formatMatch ($value='', $regexFormat='') : bool
	{	
		// Match for length '/\A\d{4}\Z/'
		if (!self::hasValue($value))  new ErrorCode(1001);
		if (!self::hasValue($regexFormat))  new ErrorCode(1001);

		return @preg_match($regexFormat, $value);
	}

	/**
	* Checks error.
    * @return bool
	*/
	final public static function checkError () : bool
	{
		return !static::hasValue(static::$error) ? TRUE : FALSE;
	}

	/**
	* Gets error.
    * @return string
	*/
	final public static function getError () : string
	{
		return static::$error;
	}
}
