<?php

namespace Blaze\Support;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Inflector\Inflector;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Collection class
 */
class Collection extends Collector
{
	protected $items = [];

	public function __construct($items)
	{
		$this->collect($items);
	}

	public function __invoke()
	{
		return $this->items();
	}

	public function collect($items)
	{
		$this->items = $items;
		return $this;
	}

	public function items() : array
	{
		return ($this->isEmpty()) ? [] : $this->items;
	}

	public function isEmpty() : bool
	{
		return empty($this->items);
	}

	public function returnItem($item)
	{
		return is_array($item) ? new static($item) : $item;
	}

	public function refresh()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$this->rewind();
		return $this->returnItem($this->items);
	}

	public function first()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->refresh()->current();
	}

	public function last()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(end($this->items));
	}

	public function keys() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_keys($this->items));
	}

	public function values() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_values($this->items));
	}

	public function has($value) : bool
	{
		return in_array($value, $this->items);
	}

	public function toJson() : string
	{
		return json_encode($this->items());
		return ($this->isEmpty()) ? "" : json_encode($this->items());
	}

	public function sum() : int
	{
		if ($this->isEmpty()) return 0;
		return $this->returnItem(array_sum($this->items));
	}

	public function walk(callable $callback=NULL, $data=NULL) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$items = $this->items();
		array_walk($items, $callback, $data);
		$this->collect($items);
		return $this->returnItem($this->items);
	}

	public function map(callable $callback=NULL, ... $items) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map($callback, $this->items, ...$items));
	}

	public function zip(... $items) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map(NULL, ...$items);
	}

	public function pluck(string $column, string $index=NULL) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_column($this->items, $column, $index));
	}

	public function trim() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map('trim');
	}

	public function uppercase() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map('strtoupper');
	}

	public function lowercase() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map('strtolower');
	}

	/**
	 * Apply ucwords function to array values to capitalize first character.
	 * @param string $delimiters
	 * @return Collection
	 */
	public function ucwords(string $delimiters=" \t\r\n\f\v") : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$this->collect($this->lowercase()->items());
		return $this->walk(function(&$value) use ($delimiters)
		{
			$value = Inflector::ucwords($value, $delimiters);
		});
	}

	public function ucfirst() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$this->collect($this->lowercase()->items());
		return $this->map('ucfirst');
	}

	// Sorting type flags:
	// SORT_REGULAR - compare items normally (don't change types)
	// SORT_NUMERIC - compare items numerically
	// SORT_STRING - compare items as strings
	// SORT_LOCALE_STRING - compare items as strings, based on the current locale.
	public function unique(int $sort_flags=SORT_STRING) : Collection
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
	public function sort(int $sort_flags=SORT_REGULAR) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		sort($this->items, $sort_flags);
		return $this->returnItem($this->items);
	}

	// Sorting type flags: @see sort()
	public function reverseSort(int $sort_flags=SORT_REGULAR) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		rsort($this->items, $sort_flags);
		return $this->returnItem($this->items);
	}

	// Flag determining what arguments are sent to callback:
	// ARRAY_FILTER_USE_KEY - pass key as the only argument to callback instead of the value
	// ARRAY_FILTER_USE_BOTH - pass both value and key as arguments to callback instead of the value
	public function filter(callable $callback=NULL, int $flag=0) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$items = (is_null($callback)) ? array_filter($this->items()) : array_filter($this->items(), $callback, $flag);
		return $this->returnItem($items);
	}

	public function flip() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_flip($this->items));
	}

	public function merge(... $items) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_merge($this->items, ...$items));
	}

	/**
	 * Join array values.
	 * @param string $glue
	 * @param string $preText
	 * @param string $postText
	 * @return mixed Collection | string
	 */
	public function join(string $glue, string $preText=NULL, string $postText=NULL)
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(trim($preText.join($glue, $this->items).$postText));
	}

	/**
	 * Concatenate pre-text to the begining and post-text to the end of each array values.
	 * @param string $preText
	 * @param string $postText
	 * @return Collection
	 */
	public function concat(string $preText=NULL, string $postText=NULL) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map(function($value) use ($preText, $postText)
		{
			return "{$preText}{$value}{$postText}";
		});
	}

	/**
	 * Slugify array values.
	 * @return Collection
	 */
	public function slugify() : Collection
	{
		$slugify = (new Slugify);
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->walk(function(&$value) use ($slugify)
		{
			$value = $slugify->slugify($value);
		});
	}

	/**
	 * Tableize array values.
	 * Ex of array values: ModelName
	 * @return Collection
	 */
	public function tableize() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->walk(function(&$value)
		{
			$value = Inflector::tableize($value);
		});
	}

	/**
	 * Classify array values.
	 * Ex of array values: model_name
	 * @return Collection
	 */
	public function classify() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->walk(function(&$value)
		{
			$value = Inflector::classify($value);
		});
	}

	/**
	 * Camelize array values.
	 * Ex of array values: model_name
	 * @return Collection
	 */
	public function camelize() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->walk(function(&$value)
		{
			$value = Inflector::camelize($value);
		});
	}

	/**
	 * Pluralize array values.
	 * @return Collection
	 */
	public function pluralize() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->walk(function(&$value)
		{
			$value = Inflector::pluralize($value);
		});
	}

	/**
	 * Singularize array values.
	 * @return Collection
	 */
	public function singularize() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->walk(function(&$value)
		{
			$value = Inflector::singularize($value);
		});
	}
}
