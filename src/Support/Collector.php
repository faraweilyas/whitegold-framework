<?php

namespace Blaze\Support;

use Iterator;
use Countable;
use Serializable;
use JsonSerializable;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Collector class
 */
class Collector implements Iterator, Countable, JsonSerializable, Serializable
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

	public function count() : int
	{
		return count($this->items);
	}

	public function jsonSerialize()
	{
		return ($this->isEmpty()) ? [] : $this->items;
	}
	
    public function serialize() : string
    {
        return serialize($this->items);
    }

    public function unserialize($items)
    {
        $this->items = unserialize($items);
    }
}
