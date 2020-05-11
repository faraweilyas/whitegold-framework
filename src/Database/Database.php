<?php

namespace Blaze\Database;

use Blaze\Logger\Log;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author iLyas Farawe <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Database Class
 */
class Database
{
	/**
	 * Stores database connection
	 * @var resource
	 */
	private $connection;
	
	/**
	 * Stores last query to the database
	 * @var string
	 */
	private static $lastQuery;

	/**
	 * Store a singleton of the Database instance.
	 * @var Blaze\Database\Database
	 */
	private static $instance;
	
	/**
	 * Open connection on instansiation.
	 */
	public function __construct()
	{
	    $this->openConnection();
	}

	/**
	 * Opens Database connection.
	 */
	final public function openConnection()
	{
		$this->connection = mysqli_connect(
			getConstant('DB_HOST', TRUE),
			getConstant('DB_USERNAME', TRUE),
			getConstant('DB_PASSWORD', TRUE),
			getConstant('DB_NAME', TRUE)
		);
		$errorMessage 	= "Database connection failed: ".mysqli_connect_error();
		$errorMessage 	.= " (" .mysqli_connect_errno(). ")";
		if (!$this->connection) die($errorMessage);
	}

    /**
     * Get an instance of the Database.
     * @return Database
     */
	public static function getInstance() : Database
	{
		return (!static::$instance) ? new static : static::$instance;
	}

	/**
	 * Returns the database connection
	 * @return \Mysqli
	 */
	final public function getConnection() : \Mysqli
	{
		return $this->connection;
	}

	/**
	 * Closes Database connection.
	 */
	final public function closeConnection()
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
	final public function query(string $sql)
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
	final public function escapeValue(string $value=NULL)
	{
		return mysqli_real_escape_string($this->connection, $value);
	}

	/**
	 * Escapes values for database query
	 * @param array $values
	 * @return array
	 */
	final public function escapeValues(array $values) : array
	{
		$sanitizedValues = [];
		foreach ($values as $key => $value):
			$key 					= $this->escapeValue($key);
			$sanitizedValues[$key]  = (is_array($value)) ? $this->escapeValues($value) : $this->escapeValue($value);
		endforeach;
		return $sanitizedValues;
	}

	/**
	 * Fetch returned array
	 * @param mixed $resultSet
	 * @return mixed
	 */
	final public static function fetchAssoc($resultSet)
	{
		return mysqli_fetch_assoc($resultSet);
	}

	/**
	 * Get the returned num rows
	 * @param mixed $resultSet
	 * @return mixed
	 */
	final public static function numRows($resultSet)
	{
		return mysqli_num_rows($resultSet);
	}

	/**
	 * Get the last id inserted over the current database connection
	 * @return int
	 */
	final public function insertId() : int
	{
		return mysqli_insert_id($this->connection);
	}

	/**
	 * Check the affected rows
	 * @return int
	 */
	final public function affectedRows() : int
	{
		return mysqli_affected_rows($this->connection);
	}

	/**
	 * Returns the last query sent to the database
	 * @return string
	 */
	final public static function lastQuery() : string
	{
		return static::$lastQuery ?: "";
	}

	/**
	 * Confirms result from database query
	 * @param mixed
	 */
	private function confirmQuery($result)
	{
		if ($result) return;
	    $outputMessage = "";
		if (getConstant('DEPLOYMENT_STATE', TRUE) == "local"):
		    $outputMessage = "Database query failed: ".mysqli_error($this->connection)."<br /><br />";
		    $outputMessage .= "Last SQL query: ".static::lastQuery();
	    else:
		    $outputMessage = "A DATABASE ERROR OCCURRED TRY AGAIN LATER OR CONTACT ADMINISTRATOR<br /><br />";
		    $log = "Database query failed: ".mysqli_error($this->connection)."\nLast SQL query: ".static::lastQuery();
		    (new Log($log, "ERROR"))->setLogFile("DBLog.txt")->logMessage();
	    endif;
	    die($outputMessage);
	}	
}
