<?php

use Blaze\Support\Collection;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Wrapper Functions
 */

if (!function_exists('collect')):
	/**
	 * Wrapper to collect an array and return a collection.
	 * @return array $items
	 * @param Collection
	 */
	function collect(array $items=[]) : Collection
	{
		return new Collection($items);
	}
endif;
