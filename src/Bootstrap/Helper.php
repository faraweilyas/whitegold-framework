<?php

use Blaze\Exception\ErrorCode;
use Blaze\RouterEngine\Router as Route;
use Blaze\Validation\Validator as Validate;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link https://faraweilyas.com
*
* Helper Functions
*/

if (!function_exists('redirectTo')):
	/**
	* Handles Redirection of links.
	* @param string $location
	* @return void
	*/
	function redirectTo(string $location=NULL) 
	{
		$location = Route::redirectRoute($location);
		header("Location: {$location}");
		exit;
	}
endif;

if (!function_exists('setConstant')):
	/**
	* Check constant if defined then don't define it.
	* @param string $name
	* @param string $value
	* @return void
	*/
	function setConstant(string $name, string $value) 
	{
		defined($name) ? NULL : define($name, $value);
	}
endif;

if (!function_exists('getConstant')):
	/**
	* Get defined constant
	* @param string $name
	* @param bool $debug
	* @return mixed
	*/
	function getConstant(string $name, bool $debug=FALSE) 
	{
		$errorMessage = $debug ? "{$name} is not defined" : "";
		return defined($name) ? constant($name) : $errorMessage;
	}
endif;

if (!function_exists('minifyHTMLOutput')):
	/**
	* Minify HTML Output
	* @return mixed
	*/
	function minifyHTMLOutput ($buffer)
	{
		$search     = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'];
		$replace    = ['>', '<', '\\1'];
		if (preg_match("/\<html/i", $buffer) == 1 && preg_match("/\<\/html\>/i", $buffer) == 1)
			$buffer = preg_replace($search, $replace, $buffer);
		return $buffer;
	}
endif;

if (!function_exists('__intended')):
	/**
	* Redirects to previous visited route.
	* @return void
	*/
	function __intended()
	{
		redirectTo(getReferer());
	}
endif;

if (!function_exists('preventFileCaching')):
	/**
	* Prevents file caching for javascript or css files by adding last modified timestamp.
	* @param string $file
	* @return string
	*/
	function preventFileCaching(string $file='') : string
	{
		if (file_exists($file)):
			$fileExtension = pathinfo($file, PATHINFO_EXTENSION);
			if (in_array($fileExtension, ['css', 'js'])):
				$lastTimeModified = filemtime($file);
			    $file .= "?mod={$lastTimeModified}";
			endif;
		endif;
		return $file;
	}
endif;

if (!function_exists('__file')):
	/**
	* Alternative function for proper file inclusion for HTML.
	* @param string $file
	* @param bool $return
	* @param string $callback
	* @return mixed
	*/
	function __file ($file='', bool $return=TRUE, string $callback="")
	{
		$file = (!empty($callback) AND function_exists($callback)) ? $callback($file) : $file;
		return Route::_file($file, $return);
	}
endif;

if (!function_exists('__url')):
	/**
	* Alternative function for proper url linking for HTML.
	* @param string $file
	* @param bool $return
	* @return mixed
	*/
	function __url ($url='', bool $return=TRUE)
	{
		return Route::_url($url, $return);
	}
endif;

if (!function_exists('layout')):
	/**
	* Require layout files.
	* @param string $layoutFile
	* @param array $definedVars
	* @return bool
	*/
	function layout(string $layoutFile=NULL, array $definedVars=[]) : bool
	{
		extract($GLOBALS, EXTR_OVERWRITE);
		extract($definedVars, EXTR_SKIP);
		$layoutFile = getConstant("LAYOUT").str_replace(".", "/", $layoutFile);
		$pathParts  = pathinfo($layoutFile);
		$layoutFile = !isset($pathParts['extension']) ? $layoutFile.".inc" : $layoutFile;
		if (!file_exists($layoutFile)):
			print $layoutFile.": File was not found!";
			return FALSE;
		endif;
		require_once $layoutFile;
		return TRUE;
	}
endif;

if (!function_exists('requireFile')):
	/**
	* Require specified file.
	* @param string $file
	* @param string $defaultExtension
	* @return array
	*/
	function requireFile(string $file, string $defaultExtension="php") : array
	{
		$extension  = pathinfo($file, PATHINFO_EXTENSION);
		$file   	= empty($extension) ? "{$file}.{$defaultExtension}" : $file;
		if (!file_exists($file) || !is_file($file)):
			print $file.": File was not found!";
			return get_defined_vars();
		endif;
		require_once $file;
		return get_defined_vars();
	}
endif;

if (!function_exists('requireMultipleFiles')):
	/**
	* Require multiple files.
	* @param array $filePaths
	* @param string $defaultExtension
	* @return array
	*/
	function requireMultipleFiles(array $filePaths, string $defaultExtension="php") : array
	{
		foreach ($filePaths as $filePath):
			$extension  = pathinfo($filePath, PATHINFO_EXTENSION);
			$filePath   = empty($extension) ? "{$filePath}.{$defaultExtension}" : $filePath;
			if (!file_exists($filePath) || !is_file($filePath)):
				print "{$filePath}: File was not found!";
				return get_defined_vars();
			endif;
			require_once $filePath;
		endforeach;
		return get_defined_vars();
	}
endif;

if (!function_exists('fieldNameAsText')):
	/**
	* Used for manipulating form values.
	* @param string $fieldName 
	* @return string
	*/
	function fieldNameAsText(string $fieldName) : string
	{
		if (!Validate::hasValue($fieldName)) new ErrorCode(1001);
		return ucwords(str_replace("_", " ", $fieldName));
	}
endif;

if (!function_exists('stripComma')):
	/**
	* Used for striping commas at the end of a string.
	* @param string $fieldName 
	* @return string
	*/
	function stripComma(string $fieldName) : string
	{
		$length = strlen(trim($fieldName));
		return substr($fieldName,0,$length-1);
	}
endif;

if (!function_exists('isFileAvailable')):
	/**
	* Checks if file is available and then return the first available one.
	* @param mixed $files 
	* @return string
	*/
	function isFileAvailable (...$files) : string
	{
		foreach ($files as $file) 
			if (file_exists($file)) return $file;
		return "";
	}
endif;

if (!function_exists('dirScanner')):
	/**
	* Scans any specified directory but throws exception if directory is invalid
	* @param string $dir
	* @return array
	*/
	function dirScanner(string $dir) : array
	{
		if (!is_dir($dir)) return [];
		return !empty(scandir($dir)) ? scandir($dir) : [];
	}
endif;

if (!function_exists('scanDirInDir')):
	/**
	* Scans any specified directory and returns directories in that directory
	* @param string $dir
	* @return array
	*/
	function scanDirInDir(string $dir) : array
	{
		$dirContents    = array_diff(dirScanner($dir), ['.', '..']);
		$newDirs        = [];
		if (empty($dirContents)) return [];
		foreach ($dirContents as $dirContent):
			$newDir = $dir.$dirContent.getConstant('DS', TRUE);
			if (is_dir($newDir)):
				$newDirs[]  = $newDir;
				$result     = scanDirInDir($newDir);
				$newDirs    = array_merge($result, $newDirs);
			endif;
		endforeach;
		return $newDirs;
	}
endif;

if (!function_exists('getFiles')):
	/**
	* Get files in specified directory
	* @param string $dir
	* @param bool $includePath
	* @return array
	*/
	function getFiles(string $dir=NULL, bool $includePath=FALSE) : array
	{
		$dir 			= substr($dir, strlen($dir) - 1) == "/" ? $dir : $dir."/";
		$dirContents 	= array_diff(dirScanner($dir), ['.', '..']);
		$files 			= [];
		foreach ($dirContents as $dirContent):
			$file = $dir.$dirContent;
			if (is_file($file) && file_exists($file)):
				$files[] = ($includePath) ? $file : $dirContent;
			endif;
		endforeach;
		return $files;
	}
endif;

if (!function_exists('renameFilesExtention')):
	/**
	* Rename files in a dir based on extension specified
	* @param string $dir
	* @param string $extension
	* @param string $newExtension
	* @param \Closure $processNamecallBack
	* @return bool
	*/
	function renameFilesExtention(string $dir=NULL, string $extension=NULL, string $newExtension=NULL, \Closure $processNamecallBack=NULL) : bool
	{
		$fileNames = getFiles($dir);
		foreach ($fileNames as $fileName):
			$pathParts = pathinfo($fileName);
			if ($extension == ($pathParts['extension'] ?? "")):
				$pathFileName = ($pathParts['filename'] ?? "");
				if (is_callable($processNamecallBack)):
					$pathFileName   = empty($processNamecallBack($pathFileName))
									? $pathFileName : $processNamecallBack($pathFileName);
				endif;
				$newFileName = $pathFileName.".$newExtension";
				rename($dir.$fileName, $dir.$newFileName);
			endif;
		endforeach;
		return TRUE;   
	}
endif;

if (!function_exists('getURLContent')):
	/**
	* Get URL Content
	* @param string $url
	* @return string
	*/
	function getURLContent(string $url) : string
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;     
	}
endif;

if (!function_exists('addPrefixToFiles')):
	/**
	* Add Prefix to all files in a dir
	* @param string $dir
	* @param string $newName
	* @param array $exceptions
	* @return bool
	*/
	function addPrefixToFiles(string $dir, string $newName, array $exceptions=[]) : bool
	{
		$fileNames = getFiles($dir);
		foreach ($fileNames as $fileName):
			$pathParts  = pathinfo($fileName);
			$file       = $pathParts['basename'] ?? "";
			if (!empty($file) AND !in_array($file, $exceptions)):
				rename($dir.$fileName, $dir.$newName.$file);
			endif;
		endforeach;
		return TRUE;   
	}
endif;

if (!function_exists('appendFile')):
	/**
	* Appends File to a specified array of directories.
	* @param string $fileName
	* @param string $directories
	* @return array
	*/
	function appendFile(string $fileName, array $directories) : array
	{
		$dirFiles = [];
		foreach ($directories as $directory)
		{
			$dirFiles[] = $directory.$fileName;
		}
		return $dirFiles;
	}
endif;

if (!function_exists('deleteFile')):
	/**
	* Deletes files without returning any error even if file doesn't exist
	* @param string $file
	* @return void
	*/
	function deleteFile ($file='')
	{
		if (file_exists($file)) unlink($file);
	}
endif;

if (!function_exists('html')):
	/**
	* Sanitize text for HTML output
	* @param string $rawString
	* @return mixed
	*/ 
	function html ($rawString=NULL) : string
	{
		return Validate::hasValue($rawString) ? htmlentities($rawString) : "";
	}
endif;

if (!function_exists('j')):
	/**
	* Sanitize text for JavaScript output
	* @param string $rawString
	* @return mixed
	*/
	function j ($rawString='')
	{
		return Validate::hasValue($rawString) ? json_encode($rawString) : "";
	}
endif;

if (!function_exists('url')):
	/**
	* Sanitize text for use in a URL
	* @param string $rawString
	* @return mixed
	*/
	function url ($rawString='')
	{
		return Validate::hasValue($rawString) ? urlencode($rawString) : "";
	}
endif;

if (!function_exists('sortArray')):
	/**
	* Sorts given array
	* @param array $arrayToSort
	* @return array
	*/
	function sortArray(array $arrayToSort) : array
	{
		usort($arrayToSort, function($a, $b)
		{
			return Validate::compareValues($a, $b);
		});
		return $arrayToSort;
	}
endif;

if (!function_exists('wordCount')):
	/**
	* Explodes words in a given string
	* @param string $word
	* @param int $option {0,1,2}
	* @param string $charactersAsWord
	* @return mixed
	*/
	function wordCount(string $word, int $option=1, string $charactersAsWord=NULL)
	{
		if (!in_array($option, [0,1,2])) new ErrorCode(1002);
		return str_word_count($word, $option, $charactersAsWord);
	}
endif;

if (!function_exists('getNavigator')):
	/**
	* Gets navigator
	* @return string
	*/
	function getNavigator() : string
	{
		return $_SERVER['HTTP_USER_AGENT'] ?? "";
	}
endif;

if (!function_exists('getReferer')):
	/**
	* Gets referer.
	* @param string $location
	* @return string
	*/
	function getReferer ($location='') : string
	{
		$location = !empty($location) ? $location : "./";
		return $_SERVER['HTTP_REFERER'] ?? $location;
	}
endif;

if (!function_exists('validateLink')):
	/**
	* Validates the link to make sure its valid for routing by removing the domain
	* @param string $link
	* @return string
	*/
	function validateLink(string $link=NULL) : string
	{
		$domain = getallheaders()['Host'];
		$link   = trim($link);
		if (!Validate::hasValue($link, $domain)) return $link;
		$length         = strlen($link);
		$domainLength   = strlen($domain) + 1;
		if (stripos($link, "http://") !== FALSE)
			return ".".substr($link, 6 + $domainLength, $length);
		if (stripos($link, "https://") !== FALSE)
			return ".".substr($link, 7 + $domainLength, $length);
		return ".".substr($link, $domainLength, $length);
	}
endif;

if (!function_exists('getUserPort')):
	/**
	* Gets user port.
	* @return string
	*/
	function getUserPort() : string
	{
		return $_SERVER['REMOTE_PORT'];
	}
endif;

if (!function_exists('getServerPort')):
	/**
	* Gets server port.
	* @return string
	*/
	function getServerPort() : string
	{
		return $_SERVER['SERVER_PORT'];
	}
endif;

if (!function_exists('getServerIP')):
	/**
	* Gets Server IP.
	* @return string
	*/
	function getServerIP() : string
	{
		return $_SERVER['SERVER_ADDR'];
	}
endif;

if (!function_exists('getHost')):
	/**
	* Get Host
	* @return string
	*/
	function getHost() : string
	{
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol.($_SERVER['HTTP_HOST'] ?? "");
	}
endif;

if (!function_exists('getHostFile')):
	/**
	* Get file's full path on server
	* @param string $fileDir
	* @param string $file
	* @return string
	*/
	function getHostFile(string $fileDir, string $file) : string
	{
		return getHost().str_replace('.', '', $fileDir).$file;
	}
endif;

if (!function_exists('getSiteURL')):
	/**
	* Gets Site URL.
	* @return string
	*/
	function getSiteURL() : string
	{
		return getHost().$_SERVER['REQUEST_URI'];
	}
endif;

if (!function_exists('checkForMatch')):
	/**
	* Check for a match of a given word
	* @param string $needle
	* @param string $haystack
	* @return bool
	*/
	function checkForMatch(string $needle, string $haystack) : bool
	{
		return preg_match("/$needle/", $haystack); 
	}
endif;

if (!function_exists('getUserDevice')):
	/**
	* Gets user device.
	* @return string
	*/
	function getUserDevice() : string
	{
		$devices    = ['iPhone', 'android', 'Windows', 'Andriod'];
		$agent      = getNavigator();
		foreach ($devices as $device)
		{
			if (checkForMatch($device, $agent)) return $device;
		}
		return "";
	}
endif;

if (!function_exists('getUserIP')):
	/**
	* Gets user IP.
	* @return string
	*/
	function getUserIP() : string
	{
		$userIP = "";
		if (isset($_SERVER['HTTP_CLIENT_IP'])) 
			$userIP = $_SERVER['HTTP_CLIENT_IP'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
			$userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		if (empty($userIP)) 
			$userIP = $_SERVER['REMOTE_ADDR'];
		return $userIP;
	}
endif;

if (!function_exists('getAge')):
	/**
	* Gets age from full birthday date.
	* Date input format: Y-m-d
	* @param string $birthdayFullDate
	* @return int
	*/
	function getAge(string $birthdayFullDate) : int
	{
		list($year, $month, $day)   = explode("-",$birthdayFullDate); 
		$yearDiff 		            = date("Y") - $year; 
		$monthDiff                  = date("m") - $month; 
		$dayDiff                    = date("d") - $day; 
		if ($dayDiff < 0 || $monthDiff < 0) $yearDiff--;
		return $yearDiff; 
	}
endif;

if (!function_exists('formatDatetime')):
	/**
	* Formats datetime in a given format with specified seperator.
	* Date input format: Y-m-d H:i:s
	* @param string $datetime
	* @param string $seperator
	* @return string
	*/
	function formatDatetime(string $datetime, string $seperator="/") : string
	{
		list($date, $time) 			= explode(" ", $datetime);
		list($year, $month, $day) 	= explode("-", $date); 
		return $year.$seperator.$month.$seperator.$day; 
	}
endif;

if (!function_exists('dateTimeToTimestamp')):
	/**
	* Converts datetime to timestamp.
	* Date input format: Y-m-d H:i:s
	* @param string $datetime
	* @return int
	*/
	function dateTimeToTimestamp(string $datetime) : int
	{
		list($date, $time)              = explode(' ', $datetime);
		list($year, $month, $day)       = explode('-', $date);
		list($hour, $minute, $second)   = explode(':', $time);
		return mktime($hour, $minute, $second, $month, $day, $year);
	}
endif;

if (!function_exists('datetimeToText')):
	/**
	* Formats the datetime to a given format.
	* Date input format: Y-m-d H:i:s
	* @param string $datetime
	* @param string $format
	* @return string
	*/
	function datetimeToText(string $datetime, string $format="fulldate") : string
	{
		$unixdatetime   = strtotime($datetime);
		$dateFormat     = "";
		switch (strtolower($format))
		{
			case 'fulldate':
				$dateFormat = "%d %B, %Y at %I:%M %p";
				break;
			case 'fulldates':
				$dateFormat = "%B %d, %Y at %I:%M %p";
				break;
			case 'date':
				$dateFormat = "%m/%d/%Y";
				break;
			case 'mysql-date':
				$dateFormat = "%m-%d-%Y";
				break;
			case 'customd':
				$dateFormat = "%d %B. %Y";
				break;
			case 'customdate':
				$dateFormat = "%d %b. %Y";
				break;
			case 'customdated':
				$dateFormat = "%d %b %Y";
				break;
			case 'monthyear':
				$dateFormat = "%b. %Y";
				break;
			case 'time':
				$dateFormat = "%I:%M %p";
				break;
			case 'datetime':
				$dateFormat = "%m/%d/%Y %H:%M:%S %p";
				break;
			case 'datefm':
				$dateFormat = "%d/%m/%Y";
				break;
			case 'datefms':
				$dateFormat = "%d-%m-%Y";
				break;
			case 'datef':
				$dateFormat = "%d/%m/%Y %H:%M:%S %p";
				break;
			case 'mysql-datetime':
				$dateFormat = "%m-%d-%Y %H:%M:%S";
				break;
			case 'word-datetime':
				$dateFormat = "%a %d %b %I:%M %p";
				break; 
			case 'word-date':
				$dateFormat = "%d %b %Y";
				break;
			case 'fullday':
				$dateFormat = "%A";
				break;
			case 'day':
				$dateFormat = "%a";
				break;      
			default:
				$dateFormat = "%B %d, %Y at %I:%M %p";
				break;
		}
		return strftime($dateFormat, $unixdatetime);
	}
endif;

if (!function_exists('timeDifference')):
	/**
	* Calculates the time difference from a given date.
	* Date input format: Y-m-d H:i:s
	* @param string $startDate
	* @param string $endDate
	* @return string
	*/
	function timeDifference(string $startDate, string $endDate) : string
	{
		$startDate 		= strtotime($startDate);
		$endDate 		= strtotime($endDate);
		$difference 	= $endDate - $startDate;

		return gmstrftime('%Hh %Mm %Ss', $difference);
	}
endif;

if (!function_exists('wordDateTime')):
	/**
	* Returns a word representation from a given date
	* Date input format: Y-m-d H:i:s
	* @param string $datetime
	* @return string
	*/
	function wordDateTime(string $datetime) : string
	{
		$timestamp = dateTimeToTimestamp($datetime);        
		if ($timestamp > time())
			return futureTime($datetime);
		else
			return backInTime($datetime);
	}
endif;

if (!function_exists('futureTime')):
	/**
	* Returns a word representation from a given date in the future
	* Date input format: Y-m-d H:i:s
	* @param string $datetime
	* @return string
	*/
	function futureTime(string $datetime) : string
	{
		$timestamp          = dateTimeToTimestamp($datetime);
		$date               = date('Y/m/d', $timestamp);
		$oneDay             = (24 * 60 * 60);
		$dateFormat         = "%a, %d %B";
		$wordPresentation   = "";

		switch ($date)
		{
			case date('Y/m/d'):
				$wordPresentation = 'Today';
				break;
			case date('Y/m/d', time() + $oneDay):
				$wordPresentation = 'Tomorrow';
				break;
			case date('Y/m/d', time() + sumUP($oneDay, 2)):
				$wordPresentation = "On ".datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() + sumUP($oneDay, 3)):
				$wordPresentation = "On ".datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() + sumUP($oneDay, 4)):
				$wordPresentation = "On ".datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() + sumUP($oneDay, 5)):
				$wordPresentation = "On ".datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() + sumUP($oneDay, 6)):
				$wordPresentation = "On ".datetimeToText($datetime, "fullday");
				break;
			default:
				$wordPresentation = strftime($dateFormat, $timestamp);
				break;
		}
		return $wordPresentation;
	}
endif;

if (!function_exists('backInTime')):
	/**
	* Returns a word representation from a given date back in time
	* Date input format: Y-m-d H:i:s
	* @param string $datetime
	* @return string
	*/
	function backInTime(string $datetime) : string
	{
		$timestamp          = dateTimeToTimestamp($datetime);
		$date               = date('Y/m/d', $timestamp);
		$oneDay             = (24 * 60 * 60);
		$dateFormat         = "%a, %d %B";
		$wordPresentation   = "";

		switch ($date)
		{
			case date('Y/m/d'):
				$wordPresentation = 'Today';
				break;
			case date('Y/m/d', time() - $oneDay):
				$wordPresentation = 'Yesterday';
				break;
			case date('Y/m/d', time() - sumUP($oneDay, 2)):
				$wordPresentation = datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() - sumUP($oneDay, 3)):
				$wordPresentation = datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() - sumUP($oneDay, 4)):
				$wordPresentation = datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() - sumUP($oneDay, 5)):
				$wordPresentation = datetimeToText($datetime, "fullday");
				break;
			case date('Y/m/d', time() - sumUP($oneDay, 6)):
				$wordPresentation = datetimeToText($datetime, "fullday");
				break;
			default:
				$wordPresentation = strftime($dateFormat, $timestamp);
				break;
		}
		return $wordPresentation;
	}
endif;

if (!function_exists('timeAgo')):
	/**
	* Get Ago Time
	* @param string $datetime
	* @param bool $full
	* @param bool $fuller
	* @return int
	*/
	function timeAgo(string $datetime=NULL, bool $full=FALSE, bool $fuller=FALSE)
	{
		$now    = new DateTime;
		$ago    = new DateTime($datetime);
		$diff   = $now->diff($ago);

		$diff->w    = floor($diff->d / 7);
		$diff->d    -= $diff->w * 7;

		$fullString     = ['y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second'];
		$shortString    = ['y' => 'y', 'm' => 'm', 'w' => 'w', 'd' => 'd', 'h' => 'h', 'i' => 'm', 's' => 's'];
		$string         = ($full) ? $fullString : $shortString;

		foreach ($string as $k => &$v):
			if ($diff->$k)
				$v = $diff->$k.' '.$v.($diff->$k > 1 ? 's' : '');
			else
				unset($string[$k]);
		endforeach;

		if (!$fuller) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string).' ago' : 'Just Now';
	}
endif;

if (!function_exists('sumUP')):
	/**
	* Sums given number with the number in given amount of times
	* @param int $number
	* @param int $amount
	* @return int
	*/
	function sumUP(int $number, int $amount) : int
	{
		$summation = 0;
		for ($i=0; $i < $amount; $i++) 
			$summation += $number;
		return $summation;
	}
endif;

if (!function_exists('readFileContent')):
	/**
	* Reads the content of a given file.
	* @param string $file
	* @return string
	*/
	function readFileContent(string $file) : string
	{
		if (!file_exists($file) OR !is_readable($file))
			return "Can't read specified file: '$file'.";
		return file_get_contents($file);
	}
endif;

if (!function_exists('jsonError')):
	/**
	* Gets last error for json encode
	* @return void
	*/
	function jsonError()
	{
		switch (json_last_error())
		{
			case JSON_ERROR_NONE:
				echo 'Error:- No errors';
			break;
			case JSON_ERROR_DEPTH:
				echo 'Error:- Maximum stack depth exceeded';
			break;
			case JSON_ERROR_STATE_MISMATCH:
				echo 'Error:- Underflow or the modes mismatch';
			break;
			case JSON_ERROR_CTRL_CHAR:
				echo 'Error:- Unexpected control character found';
			break;
			case JSON_ERROR_SYNTAX:
				echo 'Error:- Syntax error, malformed JSON';
			break;
			case JSON_ERROR_UTF8:
				echo 'Error:- Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
			default:
				echo 'Error:- Unknown error';
			break;
		}
		echo PHP_EOL;
	}
endif;

if (!function_exists('filterObject')):
	/**
	* Filter Objects.
	* @param array $objects
	* @param string $column
	* @param mixed $values
	* @return array
	*/
	function filterObject(array $objects=[], string $column=NULL, ...$values) : array
	{
		if (empty($objects))    return [];
		if (empty($column))     return [];
		if (empty($values))     return [];

		$newObject = array_filter(array_map(function ($object) use ($column, $values)
		{
			if (in_array($object->$column, $values)) return $object;
		}, $objects));
		return $newObject;
	}
endif;

if (!function_exists('joinArray')):
	/**
	* Join array values.
	* @param array $values
	* @param string $join
	* @param string $preText
	* @param string $postText
	* @return string
	*/
	function joinArray(array $values, string $join, string $preText=NULL, string $postText=NULL) : string
	{
		return trim($preText.join($join, $values).$postText);
	}
endif;

if (!function_exists('array_mpop')):
	/**
	* Pop the element off the end of array multiple times
	* @param array $array
	* @param int $iterate
	* @return array
	*/
	function array_mpop(array $array, int $iterate=1) : array
	{
		$arrayLength = count($array);
	    if ($arrayLength < 1 || $arrayLength < $iterate || $iterate < 1)
	        return $array;

	    while (($iterate--) != FALSE)
	        array_pop($array);

	    return $array;
	}
endif;

if (!function_exists('getSubDomain')):
	/**
	* Gets subdomain from url and returns string or array
	* @param string $url
	* @param int $tldLevel
	* @param bool $returnString
	* @return mixed
	*/
	function getSubDomain(string $url, int $tldLevel=1, bool $returnString=TRUE)
	{
		$parsedUrl 	= parse_url($url);
		$host 		= explode('.', $parsedUrl['host']);
		$subdomains = array_mpop($host, $tldLevel + 1);
		return ($returnString) ? joinArray($subdomains, '.') : $subdomains;
	}
endif;

if (!function_exists('getRunTime')):
	/**
	* Gets process runtime
	* @param bool $round
	* @param int $decimals
	* @return int | float
	*/
	function getRunTime(bool $round=FALSE, int $decimals=0)
	{
		$runTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
		return ($round) ? round($runTime, $decimals) : $runTime;
	}
endif;

if (!function_exists('getResourceUsage')):
	/**
	* Gets the current resource usage by script
	* @param array $resourceUsageStart
	* @param string $index
	* @return int $seconds
	*/
	function getResourceUsage(array $resourceUsageStart, string $index="utime")
	{
		$resourceUsageEnd = getrusage();
	    return ($resourceUsageEnd["ru_$index.tv_sec"] * 1000 + intval($resourceUsageEnd["ru_$index.tv_usec"] / 1000))
	     - ($resourceUsageStart["ru_$index.tv_sec"] * 1000 + intval($resourceUsageStart["ru_$index.tv_usec"] / 1000));
	}
endif;

if (!function_exists('generateRandomToken')):
	/**
	* Generates a secure random string with specified length
	* @param int $length
	* @return string
	*/
	function generateRandomToken(int $length=16) : string
	{
		if ($length <= 0) return "";
		if (version_compare(PHP_VERSION, "7.0.0", "<")):
			return bin2hex (openssl_random_pseudo_bytes($length));
		else:
			return bin2hex(random_bytes($length));
		endif;
	}
endif;
