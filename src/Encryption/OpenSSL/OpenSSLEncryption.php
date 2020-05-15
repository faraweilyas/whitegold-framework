<?php

namespace Blaze\Encryption\OpenSSL;

use Blaze\Encryption\OpenSSL\OpenSSLTrait;
use Blaze\Encryption\OpenSSL\OpenSSLEncryption;
use Blaze\Encryption\TwoWayEncryptionInterface;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * OpenSSLEncryption class
 */
class OpenSSLEncryption implements TwoWayEncryptionInterface
{
	use OpenSSLTrait;

	/**
	 * Method to configure openssl encryption parameters
	 * 
	 * @param string $method
	 * @param string $key
	 * @param bool $override
	 * @param string $level
	 * @return OpenSSLEncryption
	 */
	public function initialize(string $method=NULL, string $key=NULL, bool $override=FALSE, string $level='high') : OpenSSLEncryption
	{
		if (!$override AND $this->isConfigured()){
			// dump('HERE!', $this);
			return $this;
		}

		// ECB encrypts each block of data independently and 
		// the same plaintext block will result in the same ciphertext block.
		// $defaultMethod = 'AES-256-ECB';

		// CBC has an IV and thus needs randomness every time a message is encrypted
		$defaultMethod = 'AES-256-CBC';

		if (!empty($method)):
			($this->isMethodValid($method)) ? $this->setMethod($method) : $this->setMethod($defaultMethod);
		else:
			$this->setMethod($defaultMethod);
		endif;

		(!empty($level)) ? $this->setLevel($level) : $this->setLevel("high");
		
		(!empty($key)) ? $this->setKey($key) : $this->setDefaultKey();

		// dump('HERE!', $this);
		return $this;
	}

	/**
	 * Encrypt string
	 * 
	 * @param string $string
	 * @return string
	 */
	public function encrypt(string $string) : string
	{
		$this->initialize();
		return $this->opensslEncrypt($string, $this->key, $this->method);
	}

	/**
	 * Decrypt an encoded string
	 * 
	 * @param string $encodedString
	 * @return string
	 */
	public function decrypt(string $encodedString) : string
	{
		$this->initialize();
		return $this->opensslDecrypt($encodedString, $this->key, $this->method);
	}

	/**
	 * Encrypt string with openssl
	 * 
	 * @param string $string
	 * @param string $key
	 * @param string $method
	 * @return string
	 */
    public function opensslEncrypt(string $string, string $key, string $method) : string
    {
        $ivSize     = openssl_cipher_iv_length($method);
        $iv         = openssl_random_pseudo_bytes($ivSize);
        $encrypted  = openssl_encrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv);
        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted  = base64_encode($iv.$encrypted);
        return $encrypted;
    }

	/**
	 * Decrypt an openssl encoded string
	 * 
	 * @param string $encodedString
	 * @param string $key
	 * @param string $method
	 * @return string
	 */
    public function opensslDecrypt(string $encodedString, string $key, string $method) : string
    {
        $data   = base64_decode($encodedString);
        $ivSize = openssl_cipher_iv_length($method);
        $iv     = substr($data, 0, $ivSize);
        $string = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);
        return $string;
    }
}
