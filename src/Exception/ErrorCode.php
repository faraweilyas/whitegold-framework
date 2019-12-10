<?php

namespace Blaze\Exception;

use Blaze\Exception\ErrorHandler;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author Farawe iLyas <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * ErrorCode Class.
 */
class ErrorCode
{
	const THROW_NONE    		= 1000;
    const EMPTY_VALUE 			= 1001;
    const INVALID 				= 1002;
    const EMPTY_METHOD 			= 1003;
    const EMPTY_ARGUMENT 		= 1004;
    const INVALID_DIR 			= 1005;

	/**
	 * Constructor calls exceptionDriver() to throw exception with error codes.
	 * @param int $errorCode
	 * @return 
	 */
	public function __construct(int $errorCode=NULL)
	{
		static::exceptionDriver($errorCode);
		return;
	}

	/**
	 * Throws exception with error codes.
	 * @param int $errorCode
	 * @return 
	 */
	final public static function exceptionDriver(int $errorCode=NULL)
	{
		switch ($errorCode)
		{
			case self::EMPTY_VALUE:
				throw new ErrorHandler("Empty Value.", ErrorCode::EMPTY_VALUE);
				break;
			case self::INVALID:
				throw new ErrorHandler("Value Not Valid.", ErrorCode::INVALID);
				break;
			case self::EMPTY_METHOD:
				throw new ErrorHandler("Method not parsed in RouterEngine.", ErrorCode::EMPTY_METHOD);
				break;
			case self::EMPTY_ARGUMENT:
				throw new ErrorHandler("Argument not parsed to method in RouterEngine.", ErrorCode::EMPTY_ARGUMENT);
				break;
			case self::INVALID_DIR:
				throw new ErrorHandler("Specified directory is invalid.", ErrorCode::INVALID_DIR);
				break;
			default:
				throw new ErrorHandler('Error Not Defined.', ErrorCode::THROW_NONE);
				break;
		}
		return;
	}
}
