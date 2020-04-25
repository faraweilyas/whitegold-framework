<?php

namespace Blaze\File;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * FileImage Class
 */
class FileImage extends File
{
	public $imageResize;
	public $maxImageSize;
	public $imageJpegQuality;
	public $imageResource;

	public $imageWidth;
	public $imageHeight;

	// 3 MB
	protected $allowedFIleSize 	= 3145728;
	
	// File aLlowed mime types
	protected $allowedMimeTypes = [
		"image/png",
		"image/gif",
		"image/jpeg",
		"image/pjpeg",
	];

    /**
     * Configures file properties for evaluation upon initialization.
     * Overrides parents constructor
     * @param array $file
     * @param string $newFileName
     * @param bool $resize
     * @param int $imageSize
     * @param int $jpegQuality
     */
	function __construct(array $file, string $newFileName=NULL, bool $resize=FALSE, int $imageSize=500, int $jpegQuality=100)
	{
		parent::__construct($file, $newFileName);
		$this->imageResize 			= $resize; 
		$this->maxImageSize 		= $imageSize;
		$this->imageJpegQuality 	= $jpegQuality;
	}

	/**
	 * File Type Initialization.
	 */ 
	protected function initialize()
	{
		$this->setFileType(static::FILE_TYPE_IMAGE);
		$this->setUploadFileDir("images");
	}

	/**
	 * Validates file by it's type.
	 * @return bool
	 */
	protected function validateType() : bool
	{
		$file = $this->file;
		if (!getimagesize($file['tmp_name'])):
			$this->error = "Uploaded file is not Valid.";
			return FALSE;
		endif;
		// Get image size
		$imageSizeInfo 			= getimagesize($file['tmp_name']);
		$this->imageWidth 		= $imageSizeInfo[0];
		$this->imageHeight 		= $imageSizeInfo[1];

		// Checks if image type is allowed
		if ($this->validateMimeType() == FALSE) return FALSE;
		// Checks if image size is valid for upload
		if ($this->validateFileSize() == FALSE) return FALSE;
		// Finally uplods the file after successful evaluation
		if ($this->uploadFile() == FALSE) return FALSE;
		// Resize Imgae if resize is TRUE
		if ($this->resizeImage() == FALSE) return FALSE;
		// SUCCESS
		return TRUE;
	}

	/**
	 * Resizes the image file if resize is TRUE.
	 * @return bool
	 */
	protected function resizeImage() : bool
	{
		if ($this->imageResize) {
			$result 	= $this->createImageResource();
			if ($result == FALSE) 	return FALSE;
			$resize 	= $this->proportionalResize();
			if ($resize == FALSE) 	return FALSE;
			// Freeup memory
			imagedestroy($this->imageResource);
		}
		return TRUE;
	}

	/**
	 * Creates image resource for image resizing
	 * @return bool
	 */
	final protected function createImageResource() : bool
	{
		// SWITCH STATEMENT BELOW CREATES NEW IMAGE FROM GIVEN FILE
		switch ($this->mimeType)
		{
			case $this->allowedMimeTypes[0]:
				$this->imageResource = imagecreatefrompng($this->newFileLocation); 
				break;
			case $this->allowedMimeTypes[1]:
				$this->imageResource = imagecreatefromgif($this->newFileLocation);
				break;			
			case $this->allowedMimeTypes[2]:
			case $this->allowedMimeTypes[3]:
				$this->imageResource = imagecreatefromjpeg($this->newFileLocation);
				break;
			default:
				$this->error = "Unsupported uploaded file type try again.";
				deleteFile($this->newFileLocation);
				return FALSE;
		}
		return TRUE;
	}

	/**
	 * Construct a proportional size of new image and then creates a canvas.
	 * @return bool
	 */
	final protected function proportionalResize() : bool
	{
		// Return FALSE if nothing to resize
		if ($this->imageWidth <= 0 || $this->imageHeight <= 0):
			$this->error = "File error.";
			deleteFile($this->newFileLocation);
			return FALSE;
		endif;
		// Do not resize if image is smaller than max size
		if ($this->imageWidth <= $this->maxImageSize AND $this->imageHeight <= $this->maxImageSize) return TRUE;

		// Construct a proportional size of new image
		$imageScale		= min($this->maxImageSize / $this->imageWidth, $this->maxImageSize / $this->imageHeight);
		$newWidth		= ceil($imageScale * $this->imageWidth);
		$newHeight		= ceil($imageScale * $this->imageHeight);
		// Create a new true color image
		$newCanvas 		= imagecreatetruecolor($newWidth, $newHeight);
		// Copy and resize part of an image with resampling
		imagecopyresampled($newCanvas, $this->imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $this->imageWidth, $this->imageHeight);
		// Saves image file to destination
		return $result 	= $this->saveImageCanvas($newCanvas);
	}

	/**
	 * Saves image resource to file
	 * @param mixed $newCanvas
	 * @return bool
	 */
	final protected function saveImageCanvas($newCanvas) : bool
	{
		deleteFile($this->newFileLocation);
		// Switch statement below checks image mime type to create the new image
		switch ($this->mimeType)
		{
			case $this->allowedMimeTypes[0]:
				imagepng($newCanvas, $this->newFileLocation); return TRUE;
				break;
			case $this->allowedMimeTypes[1]:
				imagegif($newCanvas, $this->newFileLocation); return TRUE;
				break;			
			case $this->allowedMimeTypes[2]:
			case $this->allowedMimeTypes[3]:
				imagejpeg($newCanvas, $this->newFileLocation, $this->imageJpegQuality); return TRUE;
				break;
			default:
				$this->error = "An error occured.";
				return FALSE;
		}
		return TRUE;
	}
}
