<?php

namespace Blaze\Exception;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * ErrorHandler class.
 */
class ErrorHandler extends \Exception
{
	public $debug = FALSE;

	/**
	 * Redefined constructor so message isn't optional and calls the parent constructor.
	 * @param string $message
	 * @param integer $code
	 * @return string 
	 */
	public function __construct($message, $code = 0, Exception $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
	}

	/**
	 * To display custom error from an object to act as a string
	 * Magic __toString().
	 * @return string 
	 */
	public function __toString()
	{
		return $this->displayError();
	}

	/**
	 * To display custom error
	 * @return string 
	 */
	public function displayError() : string
	{
		if (!$this->debug)
			return "<b>ErrorMessage: </b> [".$this->getMessage()."] <br /><br />";
		
		$errorMessage  = "<b>ErrorMessage: </b> [".$this->getMessage()."] <br />";
		$errorMessage .= "<b>ErrorLine: </b> [".$this->getLine()."] <br />";
		$errorMessage .= "<b>ErrorFile: </b> [".$this->getFile()."] <br />";
		$errorMessage .= "<b>ErrorClass: </b> [".__CLASS__ ."] <br />";
		$errorMessage .= "<b>ErrorCode: </b> [".$this->getCode()."]. <br /><br />";
		return $errorMessage;
	}
}

/**
 * Custom Exception handler callback function.
 * @param object $exception
 */
function uncaughtExceptionHandler($exception)
{
	echo "<b>Catch uncaught error.</b><br />";
	echo $uncaughtError =  new ErrorHandler($exception->getMessage(), $exception->getCode());
}

/**
 * Custom Exception handler function.
 * @param callback
 */
set_exception_handler('Blaze\Exception\uncaughtExceptionHandler');
