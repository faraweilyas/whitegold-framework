<?php

namespace Blaze\Encryption\Mcrypt;

use Blaze\Encryption\TwoWayEncryptionInterface;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * McryptEncryption class
 */
class McryptEncryption implements TwoWayEncryptionInterface
{
    /**
     * Encrypt string
     * 
     * @param string $string
     * @return string
     */
    public function encrypt(string $string) : string
	{
		return ($this->isMcryptUsable()) ? $this->mcryptEncrypt($string) : "";
	}

    /**
     * Decrypt an encoded string
     * 
     * @param string $encodedString
     * @return string
     */
    public function decrypt(string $encodedString) : string
	{
		return ($this->isMcryptUsable()) ? $this->mcryptDecrypt($encodedString) : "";
	}

    /**
     * Check if mcrypt is usable
     * 
     * The mcrypt_encrypt function has been DEPRECATED as of PHP 7.1.0 and REMOVED as of PHP 7.2.0
     * @return bool
     */
    public function isMcryptUsable() : bool
	{
		return (version_compare(PHP_VERSION, "7.2.0", "<")) ? TRUE : FALSE;
	}

    /**
     * Two way hash algorithm to encrypt string
     * 
     * @param string $string
     * @return string
     */
    public static function mcryptEncrypt(string $string) : string
    {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        return base64_encode(mcrypt_encrypt(
        	MCRYPT_RIJNDAEL_256, md5($cryptKey), $string, MCRYPT_MODE_CBC, md5(md5($cryptKey)))
        );
    }

    /**
     * Two way hash algorithm to decrypt string
     * 
     * @param string $encodedString
     * @return string
     */
    public static function mcryptDecrypt(string $encodedString) : string
    {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        return rtrim(mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($encodedString), MCRYPT_MODE_CBC, md5(md5($cryptKey))
        ), "\0");
    }
}
