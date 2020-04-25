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
 * Collection class
 */
class Collection implements Iterator
{
	private $items = [];

	public function __construct($items)
	{
		$this->items = $items;
	}

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

	public function isEmpty() : bool
	{
		return empty($this->items);
	}

	public function returnItem($item)
	{
		return is_array($item) ? new self($item) : $item;
	}

	public function refresh()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem($this->rewind());
	}

	public function first()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->refresh();
	}

	public function last()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(end($this->items));
	}

	public function count()
	{
		return count($this->items);
	}

	public function items() : array
	{
		return ($this->isEmpty()) ? [] : $this->items;
	}

	public function zip(... $items)
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map(NULL, $this->items, ...$items));
	}

	public function pluck(string $column, string $index=NULL)
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_column($this->items, $column, $index));
	}

	public function trim()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map('trim', $this->items));
	}

	public function uppercase()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map('strtoupper', $this->items));
	}

	public function lowercase()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map('strtolower', $this->items));
	}

	public function ucwords()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map('ucwords', $this->lowercase()->items()));
	}

	public function ucfirst()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map('ucfirst', $this->lowercase()->items()));
	}

	// Sorting type flags:
	// SORT_REGULAR - compare items normally (don't change types)
	// SORT_NUMERIC - compare items numerically
	// SORT_STRING - compare items as strings
	// SORT_LOCALE_STRING - compare items as strings, based on the current locale.
	public function unique(int $sort_flags=SORT_STRING)
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_unique($this->items, $sort_flags));
	}

	// Sorting type flags:
	// SORT_REGULAR - compare items normally; the details are described in the comparison operators section
	// SORT_NUMERIC - compare items numerically
	// SORT_STRING - compare items as strings
	// SORT_LOCALE_STRING - compare items as strings, based on the current locale. It uses the locale, which can be changed using setlocale()
	// SORT_NATURAL - compare items as strings using "natural ordering" like natsort()
	// SORT_FLAG_CASE - can be combined (bitwise OR) with SORT_STRING or SORT_NATURAL to sort strings case-insensitively
	public function sort(int $sort_flags=SORT_REGULAR)
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		sort($this->items, $sort_flags);
		return $this->returnItem($this->items);
	}

	// Sorting type flags: @see sort()
	public function reverseSort(int $sort_flags=SORT_REGULAR)
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		rsort($this->items, $sort_flags);
		return $this->returnItem($this->items);
	}
}
