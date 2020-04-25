<?php

namespace Blaze\Support;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author Farawe iLyas <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Details class
 */
abstract class Details
{
	/**
	 * Page Title for HTML
	 * @var string
	 */
	protected $title;

	/**
	 * Page Description for HTML
	 * @var string
	 */
	protected $description;

	/**
	 * Constructor to set the page details.
	 * @param string $title
	 * @param string $description
	 * @return void
	 */
	public function __construct(string $title=NULL, string $description=NULL)
	{
		$this->setPageDetails($title, $description);
	}

	/**
	 * Set the page details.
	 * @param string $title
	 * @param string $description
	 * @return Details
	 */
	public function setPageDetails(string $title=NULL, string $description=NULL)
	{
		$this->title          = !empty($title) ? $title : $this->title;
		$this->description    = !empty($description) ? $description : $this->description;
		return $this;
	}

	/**
	 * Clear the page details.
	 * @param string $title
	 * @param string $description
	 * @return Details
	 */
	public function clearPageDetails()
	{
		$this->title          = "";
		$this->description    = "";
		return $this;
	}

	/**
	 * Set the title.
	 * @param string $title
	 * @return Details
	 */
	public function setTitle(string $title=NULL)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Set the description.
	 * @param string $description
	 * @return Details
	 */
	public function setDescription(string $description=NULL)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Gets the title.
	 * @return void
	 */
	public function title() : string
	{
		return $this->title ?? "";
	}

	/**
	 * Gets the description.
	 * @return void
	 */
	public function description() : string
	{
		return $this->description ?? "";
	}
}
