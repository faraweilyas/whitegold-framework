<?php

namespace Blaze\File;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * FileAttach Class
 */
class FileAttach extends File
{
	// 16 MB
	protected $allowedFIleSize 	= 16777216;

	// File aLlowed mime types
	protected $allowedMimeTypes = [];

	/**
	 * File Type Initialization.
	 */ 
	protected function initialize ()
	{
		$this->setFileType(static::FILE_TYPE_FILE);
		$this->setUploadFileDir("files");
	}

	/**
	 * Validates file by it's type.
	 * @return bool
	 */
	protected function validateType () : bool
	{
		// Checks if audio type is allowed
		if ($this->validateMimeType() == FALSE) return FALSE;
		// Checks if audio size is valid for upload
		if ($this->validateFileSize() == FALSE) return FALSE;
		// Finally uplods the file after successful evaluation
		if ($this->uploadFile() == FALSE) return FALSE;

		return TRUE;
	}	
}
