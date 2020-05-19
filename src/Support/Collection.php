<?php

namespace Blaze\Support;

use Cocur\Slugify\Slugify;
use Blaze\Support\Collection;
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
	/**
	 * Stores collection.
	 *
	 * @var array $items
	 */
	protected $items = [];

	/**
	 * Constructor method to collect array into a collection.
	 *
	 * @return void
	 */
	public function __construct($items)
	{
		$this->collect($items);
	}

	/**
	 * Magic method to be called when a collection is called as a function
     * Which returns an array of collection or empty array if collection is empty.
	 *
	 * @return array
	 */
	public function __invoke()
	{
		return $this->items();
	}

    /**
     * Magic method to be called when a collection is echoed
     * Then convert the collection to json.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

	/**
     * Collects an array and merges with existing collection.
	 *
	 * @param mixed $items
	 * @return Collection
	 */
	public function collect($items) : Collection
	{
		$existingItems 	= $this->items();
		$newItems 		= $this->override($items)->items();
		// Add array items to avoid loosing keys
		$mergedItems 	= $existingItems + $newItems;
		$this->items 	= $mergedItems;
		return $this;
	}

	/**
     * Overrides an existing collection with a new one
	 *
	 * @param mixed $items
	 * @return Collection
	 */
	public function override($items) : Collection
	{
		if ($items instanceof Collection)
			$items = $items->items();
		if (!is_array($items))
			$items = (array) $items;

		$this->items = $items;
		return $this;
	}

	/**
     * Returns an empty array if collection is empty or array of collection.
	 *
	 * @return array
	 */
	public function items() : array
	{
		return ($this->isEmpty()) ? [] : $this->items;
	}

	/**
     * Checks if collection is empty.
	 *
	 * @return bool
	 */
	public function isEmpty() : bool
	{
		return empty($this->items);
	}

	/**
     * Returns a collection if it is an array or else the value.
	 *
	 * @param mixed $item
	 * @return mixed
	 */
	public function returnItem($item)
	{
		return is_array($item) ? new static($item) : $item;
	}

	/**
     * Resets collection.
	 *
	 * @return mixed
	 */
	public function refresh()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$this->rewind();
		return $this->returnItem($this->items);
	}

	/**
     * Get first element in array.
	 *
	 * @return mixed
	 */
	public function first()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->refresh()->current();
	}

	/**
     * Get last element in array.
	 *
	 * @return mixed
	 */
	public function last()
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(end($this->items));
	}

	/**
     * Get array keys.
	 *
	 * @return Collection
	 */
	public function keys() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_keys($this->items));
	}

	/**
     * Get array values.
	 *
	 * @return Collection
	 */
	public function values() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_values($this->items));
	}

	/**
     * Checks if a value is in array.
	 *
	 * @param mixed value
	 * @return bool
	 */
	public function has($value) : bool
	{
		return in_array($value, $this->items);
	}

	/**
     * Coverts array to json.
	 *
	 * @return string
	 */
	public function toJson() : string
	{
		return json_encode($this->items());
		return ($this->isEmpty()) ? "" : json_encode($this->items());
	}

	/**
     * Sum all array items.
	 *
	 * @return int
	 */
	public function sum() : int
	{
		if ($this->isEmpty()) return 0;
		return $this->returnItem(array_sum($this->items));
	}

	/**
     * Applies array_walk with a callback on array items.
	 *
	 * @param callable $callback
	 * @param mixed $data
	 * @return Collection
	 */
	public function walk(callable $callback=NULL, $data=NULL) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$items = $this->items();
		array_walk($items, $callback, $data);
		$this->override($items);
		return $this->returnItem($this->items);
	}

	/**
     * Map a callback on array items.
	 *
	 * @param callable $callback
	 * @param ... $items
	 * @return Collection
	 */
	public function map(callable $callback=NULL, ...$items) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_map($callback, $this->items, ...$items));
	}

	/**
     * Zip the collection together with one or more arrays.
	 * 
	 * @param ... $items
	 * @return Collection
	 */
	public function zip(...$items) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map(NULL, ...$items);
	}

	/**
	 * Get a column of array values
	 * Which works on associative arrays or array of objects.
	 * 
	 * @param string $column
	 * @param string $index
	 * @return Collection
	 */
	public function pluck(string $column, string $index=NULL) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_column($this->items, $column, $index));
	}

	/**
	 * Trim all array values.
	 * 
	 * @return Collection
	 */
	public function trim() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map('trim');
	}

	/**
	 * Apply strtoupper function to array values.
	 * 
	 * @return Collection
	 */
	public function uppercase() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map('strtoupper');
	}

	/**
	 * Apply strtolower function to array values.
	 * 
	 * @return Collection
	 */
	public function lowercase() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->map('strtolower');
	}

	/**
	 * Apply ucwords function to array values to capitalize first character.
	 * 
	 * @param string $delimiters
	 * @return Collection
	 */
	public function ucwords(string $delimiters=" \t\r\n\f\v") : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$this->override($this->lowercase()->items());
		return $this->walk(function(&$value) use ($delimiters)
		{
			$value = Inflector::ucwords($value, $delimiters);
		});
	}

	/**
	 * Apply ucfirst function to array values to capitalize first character of the first word.
	 * 
	 * @return Collection
	 */
	public function ucfirst() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$this->override($this->lowercase()->items());
		return $this->map('ucfirst');
	}

	/**
	 * Get unique values of items in array.
	 * Sorting type flags:
	 * SORT_REGULAR - compare items normally (don't change types)
	 * SORT_NUMERIC - compare items numerically
	 * SORT_STRING - compare items as strings
	 * SORT_LOCALE_STRING - compare items as strings, based on the current locale.
	 * 
	 * @param int $sort_flags
	 * @return Collection
	 */
	public function unique(int $sort_flags=SORT_STRING) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_unique($this->items, $sort_flags));
	}

	/**
	 * Sort values of items in array.
	 * Sorting type flags:
	 * SORT_REGULAR - compare items normally; the details are described in the comparison operators section
	 * SORT_NUMERIC - compare items numerically
	 * SORT_STRING - compare items as strings
	 * SORT_LOCALE_STRING - compare items as strings, based on the current locale. It uses the locale, which can be changed using setlocale()
	 * SORT_NATURAL - compare items as strings using "natural ordering" like natsort()
	 * SORT_FLAG_CASE - can be combined (bitwise OR) with SORT_STRING or SORT_NATURAL to sort strings case-insensitively
	 * 
	 * @param int $sort_flags
	 * @return Collection
	 */
	public function sort(int $sort_flags=SORT_REGULAR) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		sort($this->items, $sort_flags);
		return $this->returnItem($this->items);
	}

	/**
	 * Sort values of array items in reverse.
	 * Sorting type flags: @see sort()
	 * 
	 * @param int $sort_flags
	 * @return Collection
	 */
	public function reverseSort(int $sort_flags=SORT_REGULAR) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		rsort($this->items, $sort_flags);
		return $this->returnItem($this->items);
	}

	/**
	 * Filter values of array items.
	 * Flag determining what arguments are sent to callback:
	 * ARRAY_FILTER_USE_KEY - pass key as the only argument to callback instead of the value
	 * ARRAY_FILTER_USE_BOTH - pass both value and key as arguments to callback instead of the value
	 * 
	 * @param callable $callback
	 * @param int $flag
	 * @return Collection
	 */
	public function filter(callable $callback=NULL, int $flag=0) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$items = (is_null($callback)) ? array_filter($this->items()) : array_filter($this->items(), $callback, $flag);
		return $this->returnItem($items);
	}

	/**
	 * Flip values and keys of array items.
	 * 
	 * @return Collection
	 */
	public function flip() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_flip($this->items));
	}

	/**
	 * Merge values of array items with parsed arguments.
	 * NB: if keys are numeric array_merge will reassign new keys orderly
	 * 
	 * @param ... $items
	 * @return Collection
	 */
	public function merge(...$items) : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		return $this->returnItem(array_merge($this->items, ...$items));
	}

	/**
	 * Join array values.
	 * 
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
	 * 
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
	 * 
	 * @return Collection
	 */
	public function slugify() : Collection
	{
		if ($this->isEmpty()) return $this->returnItem([]);
		$slugify = new Slugify;
		return $this->walk(function(&$value) use ($slugify)
		{
			$value = $slugify->slugify($value);
		});
	}

	/**
	 * Tableize array values.
	 * Ex: ModelName -> model_name
	 * 
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
	 * Ex: model_name -> ModelName
	 * 
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
	 * Output Ex: modelName
	 * 
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
	 * 
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
	 * 
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
