<?php

namespace Blaze\Encryption;

use Blaze\Encryption\EncryptionInterface;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * TwoWayEncryptionInterface interface
 */
interface TwoWayEncryptionInterface extends EncryptionInterface
{
	/**
	 * Decrypt encoded string
	 * 
	 * @param string $encodedString
	 * @return string
	 */
	public function decrypt(string $encodedString) : string;
}
