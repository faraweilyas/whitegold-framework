<?php	
	namespace Blaze\Database;
	
	use Blaze\{Auth\Auth, Encryption\Encrypt, Logger\Log as FileLog};

	/**
	* whiteGold - mini PHP Framework
	*
	* @package whiteGold
	* @author Farawe iLyas <faraweilyas@gmail.com>
	* @link http://faraweilyas.me
	*
	* Database Class
	*/
	class Database
	{
		// Stores database connection
		private $connection;
		
		// Stores last query to the database
		private static $lastQuery;

		// Store the single instance.
		private static $instance;

        /**
        * Get an instance of the Database.
        * @return Database
        */
		public static function getInstance () : Database
		{
			if (!static::$instance) 
				static::$instance = new static;
			return static::$instance;
		}
		
		/**
		* Open connection on instansiation.
		*/
		public function __construct ()
		{
		    $this->openConnection();
		}

		/**
		* Opens Database connection.
		*/
		final public function openConnection ()
		{
			$this->connection 	= mysqli_connect(getConstant('DB_HOST'), getConstant('DB_USERNAME'), getConstant('DB_PASSWORD'), getConstant('DB_NAME'));
			$errorMessage 		= "Database connection failed: ".mysqli_connect_error();
			$errorMessage 	   .= " (" .mysqli_connect_errno(). ")";
			if (!$this->connection) die($errorMessage);
		}

		/**
		* Returns the database connection
		* @return \Mysqli
		*/
		final public function getConnection () : \Mysqli
		{
			return $this->connection;
		}

		/**
		* Closes Database connection.
		*/
		final public function closeConnection ()
		{
			if (!isset($this->connection)) return;
			mysqli_close($this->connection);
			unset($this->connection);
		}

		/**
		* Querys the database with provided sql.
		* @param string $sql
		* @return mixed
		*/
		final public function query (string $sql)
		{
			static::$lastQuery 	= $sql;
			$result 			= mysqli_query($this->connection, $sql);
			$this->confirmQuery($result);
			return $result;
		}

		/**
		* Escapes value for database query
		* @param string $value
		* @return mixed
		*/
		final public function escapeValue (string $value=NULL)
		{
			return mysqli_real_escape_string($this->connection, $value);
		}

		/**
		* Escapes values for database query
		* @param array $values
		* @return array
		*/
		final public function escapeValues (array $values) : array
		{
			$sanitizedValues = [];
			foreach ($values as $key => $value):
				$key 					= $this->escapeValue($key);
				$sanitizedValues[$key]  = (is_array($value))
										? [$this->escapeValue($value[0]), $this->escapeValue($value[1])]
										: $this->escapeValue($value);
			endforeach;
			return $sanitizedValues;
		}

		/**
		* Fetch returned array
		* @param mixed $resultSet
		* @return mixed
		*/
		final public static function fetchAssoc ($resultSet)
		{
			return mysqli_fetch_assoc($resultSet);
		}

		/**
		* Get the returned num rows
		* @param mixed $resultSet
		* @return mixed
		*/
		final public static function numRows ($resultSet)
		{
			return mysqli_num_rows($resultSet);
		}

		/**
		* Get the last id inserted over the current database connection
		* @return int
		*/
		final public function insertId () : int
		{
			return mysqli_insert_id($this->connection);
		}

		/**
		* Check the affected rows
		* @return int
		*/
		final public function affectedRows () : int
		{
			return mysqli_affected_rows($this->connection);
		}

		/**
		* Returns the last query sent to the database
		* @return string
		*/
		final public static function lastQuery () : string
		{
			return static::$lastQuery;
		}

		/**
		* Confirms result from database query
		* @param mixed
		*/
		private function confirmQuery ($result)
		{
			if ($result) return;
		    $outputMessage = "";
			if (DEPLOYMENT_STATE == "local"):
			    $outputMessage = "Database query failed: ".mysqli_error($this->connection)."<br /><br />";
			    $outputMessage .= "Last SQL query: ".static::lastQuery();
		    else:
			    $outputMessage = "A DATABASE ERROR OCCURRED TRY AGAIN LATER OR CONTACT ADMINISTRATOR<br /><br />";
			    $log = "Database query failed: ".mysqli_error($this->connection)."\nLast SQL query: ".static::lastQuery();
			    (new FileLog($log, "ERROR"))->setLogFile("DBLog.txt")->logMessage();
		    endif;
		    die($outputMessage);
		}	
	}