<?php

namespace Blaze\File;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * FileVideo Class
 */
class FileVideo extends File
{
	// 16 MB
	protected $allowedFIleSize 	= 16777216;
	
	// File aLlowed mime types
	protected $allowedMimeTypes = [
		"video/avi", "video/msvideo","video/x-msvideo",
		"application/x-troff-msvideo", "video/mpeg",
		"video/mpeg", "video/x-motion-jpeg",
		"video/quicktime", "video/quicktime",
		"video/mpeg", "video/x-mpeg", "video/x-mpeq2a",
		"video/mpeg", "video/x-mpeg", "video/mp4",
		"video/x-matroska",
	];

	/**
	 * File Type Initialization.
	 */ 
	protected function initialize()
	{
		$this->setFileType(static::FILE_TYPE_VIDEO);
		$this->setUploadFileDir("videos");
	}

	/**
	 * Validates file by it's type.
	 * @return bool
	 */
	protected function validateType() : bool
	{
		// Checks if video type is allowed
		if ($this->validateMimeType() == FALSE) return FALSE;
		// Checks if video size is valid for upload
		if ($this->validateFileSize() == FALSE) return FALSE;
		// Finally uplods the file after successful evaluation
		if ($this->uploadFile() == FALSE) return FALSE;

		return TRUE;
	}
}
