<?php

namespace Blaze\Encryption;

use Blaze\Encryption\Encrypter;
use Blaze\Encryption\OpenSSL\OpenSSLEncryption;
use Blaze\Encryption\Blowfish\BlowFishEncryption;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Encrypt Class
 */
class Encrypt
{
	/**
	 * Encrypts password using blowfish.
	 * @param string $password
	 * @return string
	 */
	public static function passwordEncrypt(string $password=NULL) : string
	{
    	$encryption = (new Encrypter(new BlowFishEncryption()));
		return $encryption->encrypt($password);
	}

	/**
	 * Check password with existing hashed password.
	 * @param string $password
	 * @param string $existingHash
	 * @return bool
	 */
	public static function passwordCheck(string $password, string $existingHash) : bool
	{
    	$encryption = (new Encrypter(new BlowFishEncryption()));
		return $encryption->match($password, $existingHash);
	}

    /**
     * Two way hash algorithm to encrypt the given string
     * @param string $string
     * @return string
     */
    public static function blazeEncrypt(string $string) : string
    {
		$encryption = (new Encrypter(new OpenSSLEncryption()));
		return $encryption->encrypt($string);
    }

    /**
     * Two way hash algorithm to dencrypt the given string
     * @param string $encodedString
     * @return string
     */
    public static function blazeDecrypt(string $encodedString) : string
    {
		$encryption = (new Encrypter(new OpenSSLEncryption()));
		return $encryption->decrypt($encodedString);
    }

	/**
	 * Generates dynamic passkey.
     * Output ex: R.year.O.day.O.month.T : eg R15O25O12T
	 * @return string
	 */
    public static function dynamicPasskey() : string
    {
        $dateTime 					= date('Y-d-m');
        list($year, $day, $month) 	= explode('-', $dateTime);
        $year 						= substr($year, 2);
        return "R{$year}O{$day}O{$month}T";
    }
}
