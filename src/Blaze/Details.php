<?php
	namespace Blaze;
	
	/**
	* whiteGold - mini PHP Framework
	*
	* @package whiteGold
	* @author Farawe iLyas <faraweilyas@gmail.com>
	* @link http://faraweilyas.me
	*
	* Details Class
	*/
	abstract class Details
	{
		/**
		* Page Title for HTML
		* @var string
		*/
		protected static $title;
		/**
		* Page Description for HTML
		* @var string
		*/
		protected static $description;

		/**
		* Constructor to set the Page Details.
		* @param string $title
		* @param string $description
		*/
		public function __construct (string $title=NULL, string $description=NULL)
		{
			static::setPageDetails($title, $description);
		}

		/**
		* Sets the Page Details.
		* @param string $title
		* @param string $description
		*/
		protected static function setPageDetails (string $title=NULL, string $description=NULL)
		{
			self::$title          = $title;
			self::$description    = $description;
		}

		/**
		* Set the Page Title.
		* @param string $title
		*/
		public static function setTitle (string $title=NULL)
		{
			self::$title = $title;
		}

		/**
		* Set the Page Description.
		* @param string $description
		*/
		public static function setDescription (string $description=NULL)
		{
			self::$description = $description;
		}

		/**
		* Gets the Page Title.
		*/
		public static function title () : string
		{
			print self::$title;
		}

		/**
		* Gets the Page Description.
		*/
		public static function description () : string
		{
			print self::$description;
		}
	}