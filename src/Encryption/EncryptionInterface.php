<?php

namespace Blaze\Encryption;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * EncryptionInterface interface
 */
interface EncryptionInterface
{
	/**
	 * Encrypt string
	 * 
	 * @param string $string
	 * @return string
	 */
	public function encrypt(string $string) : string;
}

