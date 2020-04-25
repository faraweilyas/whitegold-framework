<?php

namespace Blaze\RouterEngine;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * RouterDaemonInterface interface
 */
interface RouterDaemonInterface
{
	/**
	 * Generates array of uris from routes.
	 * @param string $route
	 * @return array
	 */
	static function uriGenerator(string $route) : array;

	/**
	 * Generates array of uris from searched defined routes.
	 * @param string $needle
	 * @param array $haystack
	 * @return array
	 */
	static function searchUris(string $needle, array $haystack) : array;

	/**
	 * Debugger that show steps on how router works.
	 */
	static function _DEBUG();
}
