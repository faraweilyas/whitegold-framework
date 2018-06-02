<?php 
	namespace Blaze\Logger;

	use Blaze\Validation\Validator as Validate;
	
	/**
	* whiteGold - mini PHP Framework
	*
	* @package whiteGold
	* @author Farawe iLyas <faraweilyas@gmail.com>
	* @link http://faraweilyas.me
	*
	* Log Class
	*/
	class Log
	{
		protected $message;
		protected $level;
		protected $logDir = LOG;

		public $logFile;
		public $keys;
		public $delimeter;

		public $readResult;
		public $filteredResult;
		public $result;
		public $error;

		/**
		* Sets the message and level to log in class properties
		* @param string $message
		* @param string $level
		* @return void
		*/
		function __construct (string $message=NULL, string $level=NULL)
		{
			$this->message 	= $message;
			$this->level 	= strtoupper($level);
		}

		/**
		* Sets log directory
		* @param string $logDir
		* @return object
		*/
		public function setLogDir (string $logDir=NULL)
		{
			$this->logDir = $logDir;
			return $this;
		}

		/**
		* Returns the log file.
		* @param string $logFile
		* @return object
		*/
		public function setLogFile (string $logFile=NULL)
		{
			$this->logFile = $logFile;
			return $this;
		}

		/**
		* Returns the log file.
		* @return string
		*/
		public function getLogFile () : string
		{
			return $this->logFile;
		}

		/**
		* Validates the specified attribute
		* @param string $attribute
		* @return bool
		*/
		protected function validateAttribute (string $attribute=NULL) : bool
		{
			if (!property_exists($this, $attribute))
			{
				$this->error = "Attribute: ".ucfirst($attribute)." is not a class object.";
	        	return FALSE;
			}
	        $result = (is_array($this->$attribute)) ? !empty($this->$attribute) : Validate::hasValue($this->$attribute);
	        if ($result) return TRUE;
			$this->error = "Set ".ucfirst($attribute)." to log your message.";
        	return FALSE;
		}

		/**
		* Processes log file.
		* @return object
		*/
		protected function processLogFile ()
		{
			$this->logFile = (empty($this->logFile)) ? $this->logDir.strtolower($this->level)."Log.txt" : $this->logDir.$this->logFile;
			return $this;
		}

	    /**
	    * Log message in the specified log file
		* @param string $delimeter
		* @return object
	    */
	    public function logMessage (string $delimeter="|")
	    {
	    	// Validates Message
	        if ($this->validateAttribute("message") == FALSE) return $this;
	    	// Validates Message Level
	        if ($this->validateAttribute("level") == FALSE) return $this;
	    	// Validates Log File
	        if ($this->validateAttribute("logFile") == FALSE) return $this;
	        // Sets the Log File.
        	$this->processLogFile();

            // Ensure all messages have a final line return
            $datetime 		= strftime("%Y-%m-%d %H:%M:%S", time());
            $logMessage 	= "{$datetime} $delimeter {$this->level} $delimeter {$this->message}".PHP_EOL;

            // FILE_APPEND adds content to the end of the file
            // LOCK_EX forbids writing to the file while in use by us
            file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
			return $this;
	    }

	    /**
	    * Log message in the specified log file
		* @param string $delimeter
		* @param array $objects
		* @return object
	    */
	    public function fileLog (string $delimeter="|", ...$keys)
	    {
            $this->delimeter 	= $delimeter;
            $this->keys 		= $keys;
	    	// Validates Delimeter
	        if ($this->validateAttribute("delimeter") == FALSE) return $this;
	    	// Validates Keys
	        if ($this->validateAttribute("keys") == FALSE) return $this;

	        // Sets the log file.
        	$this->processLogFile();

			$logMessage = '';
        	for ($i = 0; $i < count($keys); $i++)
        	{
        		$key 		 = $keys[$i];
        		$logMessage .= ((count($keys) - 1) != $i) ? "{$key} $delimeter " : "{$key}";
        	}
            $logMessage .= PHP_EOL;

            // FILE_APPEND adds content to the end of the file
            // LOCK_EX forbids writing to the file while in use by us
            file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
			return $this;
	    }

	    /**
	    * Read log file and return this
		* @param string $logFile
		* @param array $keys
		* @param string $order
		* @param string $delimeter
		* @return this
	    */
	    public function readFile (string $logFile=NULL, array $keys=[], string $order="desc", string $delimeter="|")
	    {
			$this->readResult 	= [];
			$this->logFile 		= $logFile;
			$this->keys 		= $keys;
			$this->delimeter 	= $delimeter;

	    	// Validates Log File
	        if ($this->validateAttribute("logFile") == FALSE) return $this;
	    	// Validates Delimeter
	        if ($this->validateAttribute("delimeter") == FALSE) return $this;
	    	// Validates Keys
	        if ($this->validateAttribute("keys") == FALSE) return $this;

	        if (!file_exists($logFile)):
				$this->error = "Specified file: '$logFile' doesn't exist.";
				return $this;
			endif;

		    $logs 	= [];
		    $handle = fopen($logFile, "r");
		    if (!$handle):
				$this->error 		= "Error opening the file '$logFile'.";
				$this->readResult 	= [];
				return $this;
		    endif;
	        while (($line = fgets($handle)) !== FALSE)
	        {
	        	if (!empty(trim($line))):
		            $logObject  	= (object) [];
		            $logAttributes 	= explode($delimeter, $line);
		            for ($i = 0; $i < count($keys); $i++):
		                $key 				= $keys[$i] 			?? "";
		                $logAttribute 		= $logAttributes[$i] 	?? "";
		                $logObject->$key 	= trim($logAttribute);
		            endfor;
		            $logs[] = $logObject;
		        endif;
	        }
	        fclose($handle);
	        $order = empty($order) || !in_array(strtolower($order), ['asc', 'desc']) ? "desc" : strtolower($order);
	        if (!empty($logs) && $order == "desc") krsort($logs);
			$this->readResult 		= $logs;
			$this->filteredResult 	= $logs;
			$this->result 			= $logs;
			return $this;
	    }

	    /**
	    * Reset the filtered result
		* @return this
	    */
	    public function refresh ()
	    {
			$this->filteredResult = $this->readResult;
			return $this;
	    }

	    /**
	    * Find where a column is equal to a value
		* @param string $column
		* @param mixed $values
		* @return this
	    */
	    public function findWhere (string $column=NULL, ...$values)
	    {
	    	if (empty($this->filteredResult)) return $this;
    		$newReadResult 			= filterObject($this->filteredResult, $column, ...$values);
			$this->filteredResult 	= $newReadResult;
			$this->result 			= $newReadResult;
			return $this;
	    }

	    function test ()
	    {
		    $fileLog    = new Log;
		    // $fileLog->setLogFile("actionLog.txt")->fileLog("|", "#241561DHSW", "Follow", "Hello");
		    $readFile   = $fileLog->readFile(LOG."users.txt", ['controller', 'email', 'ceo', 'ceoEmail'], 'ASC');
		    $users      = $readFile
		                ->findWhere('controller', 'Blue')
		                ->result;
		    var_dump($fileLog, $users);
		    $users      = $fileLog
		                ->refresh()->findWhere('email', "green@gmail.com")
		                ->result;
		    var_dump($users);
	    }
	}