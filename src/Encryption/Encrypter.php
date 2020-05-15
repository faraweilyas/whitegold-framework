<?php

namespace Blaze\Encryption;

use Blaze\Encryption\EncryptionInterface;
use Blaze\Encryption\Mcrypt\McryptEncryption;
use Blaze\Encryption\OpenSSL\OpenSSLEncryption;
use Blaze\Encryption\Blowfish\BlowFishEncryption;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Encrypter Class
 */
class Encrypter
{
	/**
	 * Encryption engine
	 * 
	 * @var EncryptionInterface
	 */
	protected $encryption;

	/**
	 * Constructor to set encryption engine
	 * 
	 * @param EncryptionInterface $encryption
	 * @return void
	 */
	public function __construct(EncryptionInterface $encryption)
	{
		$this->encryption = $encryption;
	}

	/**
	 * Encrypt string
	 * 
	 * @param string $string
	 * @return string
	 */
	public function encrypt(string $string=NULL) : string
	{
		if ($this->encryption instanceof BlowFishEncryption ||
			$this->encryption instanceof OpenSSLEncryption ||
			$this->encryption instanceof McryptEncryption
		)
			return $this->encryption->encrypt($string);

		return '';
	}

	/**
	 * Decrypt string
	 * 
	 * @param string $existingHash
	 * @return string
	 */
	public function decrypt(string $existingHash) : string
	{
		if ($this->encryption instanceof OpenSSLEncryption ||
			$this->encryption instanceof McryptEncryption
		)
			return $this->encryption->decrypt($existingHash);

		return '';
	}

	/**
	 * Match string with existing hashed string
	 * 
	 * @param string $string
	 * @param string $existingHash
	 * @return bool
	 */
	public function match(string $string, string $existingHash) : bool
	{
		if ($this->encryption instanceof BlowFishEncryption)
			return $this->encryption->match($string, $existingHash);

		return '';
	}
}
