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
	* FormValidator Class
	*/
	class FormValidator extends Validator
	{
		/**
		* Validates numeric fields and length from super globals...
        * @param array $fields 
        * @param string $option
        * @param string $field 
        * @return bool
		*/
		final public static function validateNumericFields (array $fields=[], string $option, string $field) : bool
		{
			$fieldsWithError 	= "";
			$field 				= static::checkGlobal($field);
			foreach ($fields as $fieldName => $length)
			{
				$value = static::setValueNull($field[$fieldName]);
				if ($value !== NULL) {
					$options = [$option => $length];
					if (!static::hasNumber($value, $options))
						$fieldsWithError .= "'".fieldNameAsText($fieldName)."', ";
				}
			}
			static::setError($fieldsWithError);
			return static::checkError();
		}
		
		/**
		* Validates the format of super global fields...
        * @param array $fields
        * @param string $field
        * @return bool
		*/
		final public static function validateUserId (array $fields=[], string $field) : bool
		{
			$fieldsWithError 	= "";
			$field 				= static::checkGlobal($field);
			foreach ($fields as $fieldName => $format)
			{
				$value = static::setValueNull($field[$fieldName]);
				if ($value !== NULL)
				{
					if (!static::formatMatch($value, $format))
						$fieldsWithError .= "'".fieldNameAsText($fieldName)."', ";
				}
			}
			static::setError($fieldsWithError);
			return static::checkError();
		}

		/**
		* Used for whitelisting super globals.
        * @param array $allowedParams
        * @param string $option
        * @return array
		*/
		final public static function allowedParams (array $allowedParams=[], string $option) : array
		{
			$allowedArray 	= [];
			$option 		= static::checkGlobal($option);
			foreach ($allowedParams as $param) $allowedArray[$param] = $option[$param] ?? NULL;
			return $allowedArray;
		}

		/**
		* Validates presences for required fields...
        * @param array $requiredFields 
        * @param string $option 
        * @return bool
		*/
		final public static function validatePresence (array $requiredFields=[], string $option=NULL) : bool
		{
			$option 			= static::checkGlobal($option);
			$fieldsWithError 	= "";
			foreach ($requiredFields as $field):
				if (!isset($option[$field]) || !static::hasValue($option[$field])) 
					$fieldsWithError .= "'".fieldNameAsText($field)."', ";
			endforeach;
			static::setError($fieldsWithError);
			return static::checkError();
		}

		/**
		* Validates fields length from super globals...
        * @param array $fields 
        * @param string $option
        * @param string $field 
        * @return bool
		*/
		final public static function validateFields (array $fields=[], string $option, string $field) : bool
		{
			$fieldsWithError 	= "";
			$field 				= static::checkGlobal($field);
			foreach ($fields as $fieldName => $length):
				$value = static::setValueNull($field[$fieldName]);
				if ($value !== NULL)
				{
					$options = [$option => $length];
					if (!static::hasLength($value, $options))
						$fieldsWithError .= "'".fieldNameAsText($fieldName)."', ";
				}
			endforeach;
			static::setError($fieldsWithError);
			return static::checkError();
		}

		/**
		* Used for selecting super global.
        * @param string $option 
        * @return array
		*/
		final public static function checkGlobal ($option='') : array
		{
			if (!static::hasValue($option)) new ErrorCode(1001);
			$fieldGlobals = ["POST","GET","REQUEST","FILES","COOKIE","SESSION"];
			if (!in_array(strtoupper($option), $fieldGlobals)) 
				new ErrorCode(1001);
			else
				return $GLOBALS["_".strtoupper($option)];
		}

		/**
		* Set any value that is empty to NULL and if not empty returns the original value trimmed.
        * @param mixed $value
        * @return mixed
		*/
		final public static function setValueNull ($value="")
		{
			return static::hasValue($value) ? trim($value) : NULL;
		}

		/**
		* Set error.
        * @param string $errorMessage
        * @return bool
		*/
		final public static function setError (string $errorMessage=NULL) : bool
		{
			static::$error = "";
			if (!static::hasValue($errorMessage)) return FALSE;
			static::$error = "Check the following field(s) ".stripComma($errorMessage)." and try again.";
			return TRUE;
		}

		// RESET TOKEN FUNCTIONS STILL NEEDS REVISITING
		/**
		* This function generates a string that can be used as a reset token OTP...
        * @return string
		*/
		final protected static function setOtpToken () : string
		{
			return md5(uniqid(rand()));	
		}

		/**
		* Sets user OTP token to a session...
        * @param string $username
        * @param string $tokenValue
        * @return mixed
		*/
		final public static function setUserOtpToken (string $username, string $tokenValue)
		{
			// Check if user exists in database before setting OTP Token
			// Sets user OTP token to a session
			// i'm not storing it in a database to make it more secure cause its an OTP...

			if ($username) {
				$tokenTime 						= time(); 
				$_SESSION['otp_token'] 			= $tokenValue;
				$_SESSION['otp_token_time'] 	= $tokenTime;
				return $tokenValue;
			} else {
				return FALSE;
			}		
		}

		/**
		* Checks if user OTP token is recent...
        * @return bool
		*/
		final public static function isOtpTokenRecent () : bool
		{
			$maxElapsed = 60; // 1 min
			if (isset($_SESSION['otp_token_time'])) {
				$storedTime = $_SESSION['otp_token_time'];
				return ($storedTime + $maxElapsed) >= time();
			} else {
				return FALSE;
			}
		}

		/**
		* Add a new reset token to the user...
		* @param string $username
        * @return mixed
		*/
		final public static function createOtpToken (string $username)
		{
			$token = self::setOtpToken();
			return self::setUserOtpToken($username, $token);
		}

		/**
		* Remove any reset token for this user...
		* @param string $username
        * @return mixed
		*/
		final public static function deleteOtpToken (string $username)
		{
			if ($username == $_SESSION['username']) {
				$tokenValue 				= NULL;
				$tokenTime 					= NULL;
				$_SESSION['otp_token'] 		= NULL;
				$_SESSION['otp_token_time'] = NULL;
			}
			return self::setUserOtpToken($username, $tokenValue);
		}

		/**
		* Returns the user record for a given reset token If token is not found, returns NULL...
		* @param string $token
        * @return mixed
		*/
		final public static function findUserWithToken (string $token)
		{
			if (!static::hasValue($token)) {
				return NULL;
			} else {
				// self::isOtpTokenRecent();
				// $user = find_one_in_fake_db('users', 'reset_token', sql_prep($token));
				// // Note: find_one_in_fake_db returns null if not found.
				// return $user;
			}
		}

		/**
		* A function to email the reset token to the email address on file for this user.
		* @param string $username
        * @return bool
		*/
		final public static function emailResetToken (string $username) : bool
		{
			// $user = find_one_in_fake_db('users', 'username', sql_prep($username));			
			if ($user) {
				// This is where you would connect to your emailer
				// and send an email with a URL that includes the token.
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}