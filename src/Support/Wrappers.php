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
	 * Wrapper to create a collection.
	 * 
	 * @param mixed $items
	 * @return Collection
	 */
	function collect($items) : Collection
	{
		return new Collection($items);
	}
endif;
