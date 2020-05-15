<?php

namespace Blaze\Encryption\Blowfish;

use Blaze\Encryption\OneWayEncryptionInterface;


/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * BlowFishEncryption class
 */
class BlowFishEncryption implements OneWayEncryptionInterface
{

	/**
	 * Encrypt string
	 * 
	 * @param string $string
	 * @return string
	 */
	public function encrypt(string $string) : string
	{
		return $this->blowFishEncrypt($string);
	}

	/**
	 * Match string with encoded string.
	 * 
	 * @param string $string
	 * @param string $encodedString
	 * @return bool
	 */
	public function match(string $string, string $encodedString) : bool
	{
		return $this->blowFishMatch($string, $encodedString);
	}

	/**
	 * Encrypt string using blowfish.
	 * 
	 * @param string $string
	 * @return string
	 */
	public function blowFishEncrypt(string $string) : string
	{
		$hashFormat 	= "$2y$10$";
		// Tells php to use blowfish with a cost of 10
		$saltLength 	= 22;
		// Blowfish Salts should be 22-characters or more
		$salt 			= $this->generateSalt($saltLength);
		$formatAndSalt 	= $hashFormat.$salt;
		return crypt($string, $formatAndSalt);
	}

	/**
	 * Match string with existing hashed string.
	 * 
	 * @param string $string
	 * @param string $encodedString
	 * @return bool
	 */
	public function blowFishMatch(string $string, string $encodedString) : bool
	{
		// Existing hash contains format and salt at start
		$hash = crypt($string, $encodedString);
		return ($hash == $encodedString) ? TRUE : FALSE;
	}

	/**
	 * Generate salt for hashing.
	 * 
	 * @param int $length
	 * @return string
	 */
	protected function generateSalt(int $length) : string
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
}
