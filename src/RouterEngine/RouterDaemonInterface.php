<?php

namespace Blaze\RouterEngine;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link http://faraweilyas.me
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
	static function uriGenerator (string $route) : array;

	/**
	* Generates array of uris from searched defined routes.
	* @param string $needle
	* @param array $haystack
	* @return array
	*/
	static function searchUris (string $needle, array $haystack) : array;

	/**
	* Debugger that shows steps on how router works.
	*/
	static function _DEBUG ();
}
