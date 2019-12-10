<?php

namespace Blaze\Auth;

use Blaze\Exception\ErrorCode;
use Blaze\Validation\Validator as Validate;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author Farawe iLyas <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Auth Class
 */
class Auth
{
	/**
	 * Performs strick check on values parsed in.
	 * @param string $firstValue
	 * @param string $secondValue
	 * @return bool
	 */
	public static function strictCheck(string $firstValue, string $secondValue) : bool
	{
		return ($firstValue === $secondValue) ? TRUE : FALSE;
	}
}
