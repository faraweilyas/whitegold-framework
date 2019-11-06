<?php

namespace Blaze;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author Farawe iLyas <faraweilyas@gmail.com>
 * @link http://faraweilyas.me
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
	 */
	public function __construct(string $title=NULL, string $description=NULL)
	{
		$this->setPageDetails($title, $description);
	}

	/**
	 * Set the page details.
	 * @param string $title
	 * @param string $description
	 */
	protected function setPageDetails (string $title=NULL, string $description=NULL)
	{
		$this->title          = $title;
		$this->description    = $description;
	}

	/**
	* Set the title.
	* @param string $title
	*/
	public function setTitle (string $title=NULL)
	{
		$this->title = $title;
	}

	/**
	 * Set the description.
	 * @param string $description
	 */
	public function setDescription(string $description=NULL)
	{
		$this->description = $description;
	}

	/**
	 * Gets the title.
	 */
	public function title() : string
	{
		return $this->title;
	}

	/**
	 * Gets the description.
	 */
	public function description() : string
	{
		return $this->description;
	}
}
