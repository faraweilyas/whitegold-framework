<?php

namespace Blaze\Encryption\OpenSSL;

use Blaze\Encryption\OpenSSL\OpenSSLEncryption;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * OpenSSLTrait trait
 */
trait OpenSSLTrait
{
	/**
	 * OpenSSL encryption level
	 * 
	 * @var string
	 */
	protected $level;

	/**
	 * OpenSSL encryption key
	 * 
	 * @var string
	 */
	protected $key;

	/**
	 * OpenSSL encryption method
	 * 
	 * @var string
	 */
	protected $method;

	/**
	 * Set openssl key
	 * 
	 * @param string $key
	 * @return OpenSSLEncryption
	 */
    public function setKey(string $key=NULL) : OpenSSLEncryption
    {
    	$this->key = $key;
    	return $this;
    }

	/**
	 * Set openssl default key
	 * 
	 * @return OpenSSLEncryption
	 */
    public function setDefaultKey() : OpenSSLEncryption
    {
    	// Simple password hash
		$randomPassword 	= "ZZaUGc#*f5:>LWF";
		$simplekey 			= hash('sha256', $randomPassword);
		$strongKey 			= 'b"\vÚ¼┼ú­K\eÙ‗¾s æüú"'; // $this->generateStrongKey();
		$key 				= ($this->level == 'high') ? $simplekey : $strongKey;
    	$this->setKey($key);
    	return $this;
    }

	/**
	 * Generate a strong key to use for openssl encryption
	 * Most secure, you must store this secret random key in a safe place in your system.
	 * 
	 * @param string $method
	 * @return string
	 */
    public function generateStrongKey(string $method=NULL)
    {
		$method = empty($method) ? $this->method : $method;
    	return openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    }

	/**
	 * Set openssl encryption level
	 * 
	 * @param string $level
	 * @return OpenSSLEncryption
	 */
    public function setLevel(string $level='high') : OpenSSLEncryption
    {
    	$this->level = $level;
    	return $this;
    }

	/**
	 * Set openssl method
	 * 
	 * @param string $method
	 * @return OpenSSLEncryption
	 */
    public function setMethod(string $method=NULL) : OpenSSLEncryption
    {
    	$this->method = $method;
    	return $this;
    }

	/**
	 * Check if openssl method is valid
	 * 
	 * @return bool
	 */
    public function isMethodValid() : bool
    {
    	return (in_array($this->method, $this->getMethods())) ? TRUE : FALSE;
    }

	/**
	 * Get openssl methods
	 * 
	 * @return array
	 */
    public function getMethods() : array
    {
    	return openssl_get_cipher_methods();
    }

	/**
	 * Check if openssl parameters are configured
	 * 
	 * @return bool
	 */
    public function isConfigured() : bool
    {
    	if (empty($this->method) || !$this->isMethodValid())
    		return FALSE;

    	if (empty($this->key))
    		return FALSE;

    	return TRUE;
    }
}
