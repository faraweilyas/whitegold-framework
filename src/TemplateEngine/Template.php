<?php

namespace Blaze\TemplateEngine;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link http://faraweilyas.me
*
* Template Class
*/
abstract class Template
{			
	private $file;
	private $location;
	private $assignedVariables 	= [];
	
	/**
	* Open connection on instansiation.
	*/
	public function __construct ()
	{
	    $this->location = getConstant("TEMPLATE", TRUE);
	}

	/**
	* Sets the assignedVariables
	* @param mixed $key
	* @param mixed $value
	*/
	final protected function set ($key, $value)
	{
		$this->assignedVariables[$key] = $value;
	}

	/**
	* Set the template location
	* @param string $location
	* @return void
	*/
	final protected function setLocation (string $location)
	{
		$this->location = $location;
	}

	/**
	* Get the template location
	* @return string
	*/
	final public function getLocation () : string
	{
    	return empty($this->location) ? getConstant("TEMPLATE", TRUE) : $this->location;
	}

	/**
	* Sets the template file
	* @param string $fileLocation
	*/
	final protected function setFile (string $fileLocation)
	{
		$this->file = $fileLocation;
	}

	/**
	* Sets the template file location
	* @param string $file
	*/
	final protected function setFileLocation (string $file)
	{
        $file 		= $this->getLocation().$file;
        $pathParts  = pathinfo($file);
        $this->file = !isset($pathParts['extension']) ? $file.".inc" : $file;
	}
	
	/**
	* Displays the template being generated
	* @param bool $return
	* @return mixed
	*/
	final protected function display (bool $return=FALSE)
	{
		if (file_exists($this->file)):
			$output = file_get_contents($this->file);
			foreach ($this->assignedVariables as $key => $value) 
				$output = preg_replace('/{'.$key.'}/', $value, $output);
		else:
			$output = "*** Missing template error: {$this->file} ***";
		endif;

		if ($return)
			return $output;
		else
			echo $output;
	}
}
