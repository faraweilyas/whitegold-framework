<?php

namespace Blaze\File;

use Blaze\Validation\Validator as Validate;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link https://faraweilyas.com
*
* File Class
*/
abstract class File
{
	public $file;
	public $fileType;

	protected $fileName;
	protected $mimeType;
	protected $tempPath;
	protected $fileError;
	protected $fileSize;

	protected $fileExtension;
	protected $allowedFIleSize 	= 5242880;
	protected $uploadDir;
	protected $uploadFileDir 	= "";

	public $error;
	public $newFileName;
	public $newFileLocation;

    const FILE_TYPE_IMAGE 	= 1;
    const FILE_TYPE_VIDEO 	= 2;
    const FILE_TYPE_AUDIO 	= 3;
    const FILE_TYPE_FILE 	= 4;

    // File types.
    static protected $fileTypes = [
    	File::FILE_TYPE_IMAGE 	=> 'image',
    	File::FILE_TYPE_VIDEO 	=> 'video',
    	File::FILE_TYPE_AUDIO 	=> 'audio',
    	File::FILE_TYPE_FILE 	=> 'file',
    ];

	protected $fileUploadErrors = [
		UPLOAD_ERR_OK  			=> "No errors.",
		UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
		UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
		UPLOAD_ERR_PARTIAL  	=> "Partial upload.",
		UPLOAD_ERR_NO_FILE 		=> "No file.",
		UPLOAD_ERR_NO_TMP_DIR 	=> "No temporary directory.",
		UPLOAD_ERR_CANT_WRITE 	=> "Can't write to disk.",
		UPLOAD_ERR_EXTENSION  	=> "File upload stopped by extension."
	];

    /**
    * Configures file properties for evaluation upon initialization.
    * @param array $file
    * @param string $newFileName
    */
	function __construct (array $file, string $newFileName=NULL)
	{
		$this->initialize();
		$this->file  			= $file;
		$this->newFileName 		= $newFileName;
	}

    /**
    * Extending classes must implement initialization method.
    */
    abstract protected function initialize ();

    /**
    * Extending classes must implement validateType method.
    */
    abstract protected function validateType () : bool;

    /**
    * Determine if file type is valid.
    * @param int $fileType
    * @return boolean 
    */
    public static function isFileTypeValid (int $fileType)
    {
        return array_key_exists($fileType, self::$fileTypes);
    }

    /**
    * if valid set the file type property.
    * @param int $fileType
    */
    public function setFileType (int $fileType)
    {
        if (self::isFileTypeValid($fileType)) $this->fileType = self::$fileTypes[$fileType];
    }

    /**
    * Set the upload dir.
    * @param string $uploadDir
	* @return void
    */
    public function setUploadDir (string $uploadDir)
    {
    	$this->uploadDir = $uploadDir;
    }

    /**
    * Get the upload dir.
	* @return string
    */
    public function getUploadDir () : string
    {
    	return empty($this->uploadDir) ? getConstant("UPLOAD", TRUE) : $this->uploadDir;
    }

    /**
    * Set the file upload dir.
    * @param string $uploadFileDir
    */
    public function setUploadFileDir (string $uploadFileDir)
    {
    	$this->uploadFileDir = $uploadFileDir;
    }

    /**
    * Set the file allowed size.
    * @param int $allowedFIleSize
    */
    public function setAllowedFIleSize (int $allowedFIleSize)
    {
    	$this->allowedFIleSize = $allowedFIleSize;
    }

    /**
    * Set the allowed mime types.
    * @param array $allowedMimeTypes
    */
    public function setAllowedMimeTypes (...$allowedMimeTypes)
    {
    	if (is_array($allowedMimeTypes[0]))
        	$this->allowedMimeTypes 	= array_merge($this->allowedMimeTypes, $allowedMimeTypes[0]);
        else
        	$this->allowedMimeTypes 	= array_merge($this->allowedMimeTypes, $allowedMimeTypes);
    }

	/**
	* Processes the file.
	* @return bool
	*/
	final public function processFile () : bool
	{
		// Validate the file
		if ($this->validateFile() == FALSE) return FALSE;
		// Set the attributes
		$this->setAttributes();
		// Validate the file depending on the file type.
		if ($this->validateType() == FALSE) return FALSE;
		// SUCCESS.
		return TRUE;
	}

	/**
	* Validates the file
	* @return bool
	*/
	final protected function validateFile () : bool
	{
		$file = $this->file;
		// Perform error checking on the file parameter parsed in
		if (!$file || empty($file) || !is_array($file)):
			// Error: nothing uploaded or wrong argument usage
			$this->error = "No file was uploaded.";
			return FALSE;
		endif;
		// Check if file is not empty
		if (!isset($file) || !is_uploaded_file($file['tmp_name'])):
			// Error: in file upload
			$this->error = "Uploaded file is Missing.";
			return FALSE;
		endif;
		// Check if file doesn't contain any error
		if ($file['error'] != 0):
			// Error: report what php says went wrong
			$this->error = $this->fileUploadErrors[$file['error']];
			return FALSE;
		endif;
		return TRUE;
	}

    /**
    * Sets file attributes to class properties for evaluation
    */
	protected function setAttributes ()
	{
		$file 					= $this->file;	
		$this->fileName 		= $file['name'];
		$this->mimeType 	 	= $file['type'];
		$this->tempPath 		= $file['tmp_name'];
		$this->fileError 		= $file['error'];
		$this->fileSize 		= $file['size'];

		$pathParts 				= pathinfo($this->fileName);
		$this->fileExtension  	= isset($pathParts['extension']) ? ".".strtolower($pathParts['extension']) : "";
		$newUniqueName 			= uniqid("", TRUE).$this->fileExtension;
		$this->newFileName 		= Validate::hasValue($this->newFileName) ? $this->newFileName.$this->fileExtension : $newUniqueName;
	}

	/**
	* Validates file mime type.
	* @return bool
	*/
	protected function validateMimeType () : bool
	{
		if (!is_array($this->allowedMimeTypes) || empty($this->allowedMimeTypes))
			return TRUE;
		if (!in_array($this->mimeType, $this->allowedMimeTypes)):
			$this->error = "Unsupported file type.";
			return FALSE;
		endif;
		return TRUE;
	}

	/**
	* Uploads file to detected destination
	* @return bool
	*/
	protected function uploadFile () : bool
	{
		// Determine the destination
		$this->newFileLocation = $this->getUploadDir().$this->uploadFileDir.getConstant('DS', TRUE).$this->newFileName;			  
		// Make sure a file doesn't already exist in the target location
		if (file_exists($this->newFileLocation)):
			$this->error = "The file {$this->newFileLocation} already exists.";
			return FALSE;
		endif;
		// Attempt to move/upload the file 
		if (!move_uploaded_file($this->tempPath, $this->newFileLocation)):
			// File was not moved - failure.
			$this->error = "The file upload failed.";
			return FALSE;
		endif;
		// SUCCESS
		return TRUE;
	}

	/**
	* Validates file size.
	* @param int $allowedSize
	* @return bool
	*/
	protected function validateFileSize (int $allowedSize=0) : bool
	{
		$allowedSize = (!empty($allowedSize) AND $allowedSize >= 1) ? $allowedSize : $this->allowedFIleSize;
		if ($this->fileSize < $allowedSize) return TRUE;
		$this->error = "File is too large try another one.";
		return FALSE;			
	}

	/**
	* Converts size in bytes to a readable text
	* @param int $bytes
	* @return string
	*/
	final static public function sizeBytesToText (int $bytes=0) : string
	{
		if ($bytes >= pow(1024,8)) 			return number_format($bytes / pow(1024,8), 2) . ' YB';
		elseif ($bytes >= pow(1024,7)) 		return number_format($bytes / pow(1024,7), 2) . ' ZB';
		elseif ($bytes >= pow(1024,6)) 		return number_format($bytes / pow(1024,6), 2) . ' EB';
		elseif ($bytes >= pow(1024,5)) 		return number_format($bytes / pow(1024,5), 2) . ' PB';
		elseif ($bytes >= pow(1024,4)) 		return number_format($bytes / pow(1024,4), 2) . ' TB';
		elseif ($bytes >= pow(1024,3)) 		return number_format($bytes / pow(1024,3), 2) . ' GB';
		elseif ($bytes >= pow(1024,2)) 		return number_format($bytes / pow(1024,2), 2) . ' MB';
		elseif ($bytes >= pow(1024,1)) 		return number_format($bytes / pow(1024,1), 2) . ' KB';		
		elseif ($bytes > 1) 				return $bytes . ' bytes';		
		elseif ($bytes <= 1) 				return $bytes . ' byte';
	}

	/**
	* Converts size text to bytes.
	* @param int $size
	* @param string $unit
	* @return int
	*/
	final static public function sizeTextToBytes (int $size=0, string $unit="") : int
	{
		$units = ['B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8 ];
		return $size * pow(1024, $units[strtoupper($unit)]);
	}
}
