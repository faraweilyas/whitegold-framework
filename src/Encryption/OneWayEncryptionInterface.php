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
 * OneWayEncryptionInterface interface
 */
interface OneWayEncryptionInterface extends EncryptionInterface
{
	/**
	 * Match string with encoded string.
	 * 
	 * @param string $string
	 * @param string $encodedString
	 * @return bool
	 */
	public function match(string $string, string $encodedString) : bool;
}
