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
	 * @return mixed array | Collection $items
	 * @param Blaze\Support\Collection
	 */
	function collect($items) : Collection
	{
		return ($items instanceof Collection) ? $items : new Collection($items);
	}
endif;
