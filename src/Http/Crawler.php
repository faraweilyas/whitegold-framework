<?php

namespace Blaze\Http;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link https://faraweilyas.com
*
* Crawler Class
*/
abstract class Crawler
{
	/**
	* Stores links to crawl
	* @var array
	*/
	protected $urls 		= [];

	/**
	* Stores fields that are to be parsed with the url
	* @var array
	*/
	protected $fields 		= [];

	/**
	* Stores errors.
	* @var string
	*/
	protected $error 		= '';

	/**
	* Stores the curl handle resource
	* @var resource
	*/
	protected $curlHandle;		

	/**
	* Constructor to initialize connection.
	*/
	public function __construct()
	{
		$this->openConnection();
	}

	/**
	* Create a new curl connection.
	*/
	protected function openConnection ()
	{
		$this->curlHandle = curl_init();
	}

    /**
    * Gets the error.
    * @return string
    */
    public function getError () : string
    {
        return $this->error;
    }

	/**
	* Sets links to be visited
	* @param array $urls
	*/
	protected function setUrls (array $urls)
	{
		foreach ($urls as $key => $value) $this->urls[$key] = $value;
	}

	/**
	* Validates the connection.
	* @return bool
	*/
	protected function validateConnection () : bool
	{
		if (curl_exec($this->curlHandle)) return TRUE;
		$this->error = "An error has occurred: ".curl_error($this->curlHandle);
		return FALSE;			
	}

	/**
	* Validates the result from a session
	* @param mixed $result
	* @return bool
	*/
	protected function validateResult ($result) : bool
	{
		if (!is_string($result)) return TRUE;
		if (stripos($result, "An error") !== 0) return TRUE;
		$this->error = $result;
		return FALSE;
	}

	/**
	* Sets post data to be sent
	* Expects an associative array
	* @param array $fields
	*/
	protected function setPostFields (array $fields)
	{
		foreach ($fields as $key => $value) $this->fields[$key] = $value;
	}

	/**
	* Generate the post fields to be sent
	* @return string
	*/
	protected function generatePostFields () : string
	{
		$fieldsString = '';
		foreach($this->fields as $key => $value) $fieldsString .= $key.'='.$value.'&';
		return rtrim($fieldsString, '&');
	}

	/**
	* Set default curl configurations
	* @param string $cookieLocation
	*/
	protected function setDefaultOptions (string $cookieLocation='')
	{
		$cookie = $cookieLocation."cookie.txt";
		// MAKE IT RETURN RESULT INSTEAD OF DISPLAYING
		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
		// MAKE IT REDIRECT
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, TRUE);
		// SET THE SESSION OPTION
		curl_setopt($this->curlHandle, CURLOPT_COOKIESESSION , TRUE);
		// SET THE COOKIE FILE
		curl_setopt($this->curlHandle, CURLOPT_COOKIEFILE, $cookie);
		// SET THE COOKIE JAR FILE
		curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $cookie);
	}

	/**
	* Close curl connection
	*/
	protected function closeConnection ()
	{
		curl_close($this->curlHandle);
	}

	/**
	* Returns information on current curl connection.
	* @return array
	*/
	public function curlInfo () : array
	{
        return curl_getinfo($this->curlHandle);
	}
}
