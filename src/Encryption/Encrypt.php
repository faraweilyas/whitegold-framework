<?php

namespace Blaze\Encryption;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link http://faraweilyas.me
*
* Encrypt Class
*/
class Encrypt
{
	/**
	* Generates salt for hashing.
	* @param int $length
	* @return string
	*/
	private static function generateSalt (int $length) : string
	{
		// Not 100% unique, not 100% random, but good enough for a salt
		// MD5 returns 32 characters
		$uniqueRandomString = md5(uniqid(mt_rand(),true));

		// Valid Characters for a salt are [a-AZ-Z0-9./]
		$base64String = base64_encode($uniqueRandomString);

		// But no '+' which is valid in base64 encoding
		$modifiedBase64String = str_replace('+', '.', $base64String);

		// Truncate String to the correct length
		return substr($modifiedBase64String, 0, $length);
	}

	/**
	* Encrypts password using blowfish.
	* @param string $password
	* @return string
	*/
	public static function passwordEncrypt (string $password=NULL) : string
	{
		$hashFormat 	= "$2y$10$";
		// Tells php to use blowfish with a cost of 10
		$saltLength 	= 22;
		// Blowfish Salts should be 22-characters or more
		$salt 			= static::generateSalt($saltLength);
		$formatAndSalt 	= $hashFormat.$salt;
		return crypt($password, $formatAndSalt);
	}

	/**
	* Check password with existing hashed password.
	* @param string $password
	* @param string $existingHash
	* @return bool
	*/
	public static function passwordCheck (string $password, string $existingHash) : bool
	{
		// Existing hash contains format and salt at start
		$hash = crypt($password, $existingHash);
		return ($hash == $existingHash) ? TRUE : FALSE;
	}

	/**
	* Generates dynamic passkey.
    * Output ex: R.year.O.day.O.month.T : eg R15O25O12T
	* @return string
	*/
    public static function dynamicPasskey () : string
    {
        $dateTime 					= date('Y-d-m');
        list($year, $day, $month) 	= explode('-', $dateTime);
        $year 						= substr($year, 2);
        return "R{$year}O{$day}O{$month}T";
    }

    /**
    * Two way hash algorithm to encrypt the given string
    * @param string $string
    * @return string
    */
    public static function blazeEncrypt (string $string) : string
    {
        $cryptKey           = 'qJB0rGtIn5UB1xG03efyCp';
        return base64_encode(mcrypt_encrypt(
        	MCRYPT_RIJNDAEL_256, md5($cryptKey), $string, MCRYPT_MODE_CBC, md5(md5($cryptKey)))
        );
    }

    /**
    * Two way hash algorithm to dencrypt the given string
    * @param string $encodedString
    * @return string
    */
    public static function blazeDecrypt (string $encodedString) : string
    {
        $cryptKey       = 'qJB0rGtIn5UB1xG03efyCp';
        return rtrim(mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($encodedString), MCRYPT_MODE_CBC, md5(md5($cryptKey))
        ), "\0");
    }
}
