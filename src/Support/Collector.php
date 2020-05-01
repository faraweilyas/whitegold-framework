<?php

namespace Blaze\Support;

use Iterator;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Collector class
 */
class Collector implements Iterator
{
	public function rewind()
	{
		return reset($this->items);
	}

	public function current()
	{
		return current($this->items);
	}

	public function key()
	{
		return key($this->items);
	}

	public function next()
	{
		return next($this->items);
	}

	public function valid()
	{
		return key($this->items) !== NULL;
	}
}
