<?php
	namespace Blaze\Database;

	use Blaze\{Database\Database, Validation\Validator as Validate};

	/**
	* whiteGold - mini PHP Framework
	*
	* @package whiteGold
	* @author Farawe iLyas <faraweilyas@gmail.com>
	* @link http://faraweilyas.me
	*
	* DatabaseObject Class
	*/
	class DatabaseObject extends DatabaseParts
	{
		/** 
		* Stores the database table name
		* @var string
		*/
		protected static $tableName 		= "";
		/** 
		* Stores the database columns
		* @var array
		*/
		protected static $databaseFields 	= [];
		/** 
		* Stores the found database rows as an object.
		* @var mixed
		*/
		protected static $foundObject;

	    /**
	    * Returns database table name property
	    * @return string
	    */
		public static function getTableName () : string
		{
			return static::$tableName;
		}

	    /**
	    * Returns database fields property
	    * @return array
	    */
		public static function getDatabaseFields () : array
		{
			return static::$databaseFields;
		}

	    /**
	    * Returns foundObject property
	    * @return mixed
	    */
		public static function getFoundObject ()
		{
			return static::$foundObject;
		}

		/**
		* Update or Create new records.
		* A new record won't have an id yet.
		* @return bool
		*/
		public function save () : bool
		{
			return isset($this->id) ? $this->update() : $this->create();
		}

		/**
		* Create new record in the database.
		* @return bool
		*/
		public function create () : bool
		{
            $dbObject 	= Database::getInstance();
			$attributes = $this->sanitizedAttributes();
			$sqlQuery	= "INSERT INTO ".static::$tableName." (";
			$sqlQuery  .= join(", ", array_keys($attributes));
		  	$sqlQuery  .= ") VALUES ('";
			$sqlQuery  .= join("', '", array_values($attributes));
			$sqlQuery  .= "')";
			$result 	= $dbObject->query($sqlQuery);
	    	$this->id 	= $dbObject->insertId();
	    	return ($result) ? TRUE : FALSE;
		}

		/**
		* Update new record in the database.
		* @return bool
		*/
		public function update () : bool
		{
            $dbObject 			= Database::getInstance();
			$attributes 		= $this->sanitizedAttributes();
			$attributePairs 	= static::generateKeyValue($attributes);

			$sqlQuery 			= "UPDATE ".static::$tableName." SET ";
			$sqlQuery 		   .= join(", ", $attributePairs);
			$sqlQuery 		   .= " WHERE id=". $dbObject->escapeValue($this->id);
			$result 			= $dbObject->query($sqlQuery);
	    	return ($result) ? TRUE : FALSE;
		}

		/**
		* Delete a record from the database
		* @return bool
		*/
		public function delete () : bool
		{
            $dbObject  = Database::getInstance();
			$sqlQuery  = "DELETE FROM ".static::$tableName." WHERE id=";
			$sqlQuery .= $dbObject->escapeValue($this->id)." LIMIT 1";
			$dbObject->query($sqlQuery);
			return ($dbObject->affectedRows() == 1) ? TRUE : FALSE;
		}

		/**
		* Delete a record from the database where a given column is equal to the given value 
		* @param string $column
		* @param string $value
		* @return bool
		*/
		public static function deleteWhere (string $column, string $value) : bool
		{
            $dbObject  = Database::getInstance();
			$sqlQuery  = "DELETE FROM ".static::$tableName." WHERE ";
			$sqlQuery .= $dbObject->escapeValue($column)."=";
			$sqlQuery .= $dbObject->escapeValue($value)." LIMIT 1";
			$dbObject->query($sqlQuery);
			return ($dbObject->affectedRows() == 1) ? TRUE : FALSE;
		}

		/**
		* Find all from the database
		* @param string $order
		* @return array
		*/
		public static function findAll (string $order="DESC") : array
		{
			$order = static::validateOrder($order);
			return static::findBySql("SELECT * FROM ".static::$tableName." ORDER BY id {$order}");
		}

		/**
		* Group by
		* @param string $field
		* @param string $order
		* @return array
		*/
		public static function groupBy (string $field, string $order="DESC") : array
		{
			$order = static::validateOrder($order);
			return static::findBySql("SELECT * FROM ".static::$tableName." GROUP BY {$field} ORDER BY id {$order}");
		}

		/**
		* Find Columns from the database
		* @param string $column
		* @param string $order
		* @return array
		*/
		public static function findColumn (string $column, string $order="DESC") : array
		{
			$order 		= static::validateOrder($order);
            $dbObject 	= Database::getInstance();
            $column 	= $dbObject->escapeValue($column);
			return static::findBySql("SELECT {$column} FROM ".static::$tableName." ORDER BY id {$order}");
		}

		/**
		* Find Columns from the database
		* working on it
		* @param string $column
		* @param string $order
		* @return array
		*/
		public static function findColumns (string $column, string $order="DESC") : array
		{
            $dbObject  		= Database::getInstance();
			$order 	   		= static::validateOrder($order);
            $columnsValues 	= $dbObject->escapeValues($columnsValues);
			$attributePairs = static::generateKeyValue($columnsValues);

			$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
			$sqlQuery 	   .= join(" AND ", $attributePairs);
			$sqlQuery      .= " ORDER BY id {$order}";
			return static::findBySql($sqlQuery);
			
			$order 		= static::validateOrder($order);
            $dbObject 	= Database::getInstance();
            $column 	= $dbObject->escapeValue($column);
			return static::findBySql("SELECT {$column} FROM ".static::$tableName." ORDER BY id {$order}");
		}

		/**
		* Search the database
		* @param string $searchQ
		* @param string $column
		* @param string $order
		* @return array
		*/
		public static function search (string $searchQ, string $column, string $order="DESC") : array
		{
			$order 		= static::validateOrder($order);
			$sqlQuery 	= "SELECT * FROM ".static::$tableName." WHERE {$column} LIKE '%$searchQ%' ORDER BY id {$order}";
			return static::findBySql($sqlQuery);
		}

		/**
		* Find some of the database rows by limiting it.
		* @param int $limit
		* @param string $order
		* @return array
		*/
		public static function findSome (int $limit=5, string $order="DESC") : array
		{
			$order = static::validateOrder($order);
			return static::findBySql("SELECT * FROM ".static::$tableName." ORDER BY id {$order} LIMIT {$limit}");
		}

		/**
		* Returns found column as an object otherwise FALSE
		* @param int $id
		* @return mixed
		*/
		public static function findById (int $id)
		{
            $dbObject  		= Database::getInstance();
			$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE id=".$dbObject->escapeValue($id)." LIMIT 1";
			$resultArray 	= static::findBySql($sqlQuery);
			return !empty($resultArray) ? array_shift($resultArray) : FALSE;
		}

		/**
		* Find where a given column is equal to a given value and return the first element in the array.
		* @param string $column
		* @param string $value
		* @return mixed
		*/
		public static function findByColumn (string $column, string $value)
		{
            $dbObject  		= Database::getInstance();
			$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
			$sqlQuery 	   .= $dbObject->escapeValue($column)." = '";
			$sqlQuery 	   .= $dbObject->escapeValue($value)."' LIMIT 1";
			$resultArray 	= static::findBySql($sqlQuery);
			return !empty($resultArray) ? array_shift($resultArray) : FALSE;
		}

		/**
		* Find where a given column is equal to a given value (Multiple Columns).
		* $columnsValues takes an assoc array.
		* @param array $columnsValues
		* @param string $expression
		* @return mixed
		*/
		public static function findMultipleColumn (array $columnsValues, string $expression="AND")
		{
            $dbObject  		= Database::getInstance();
            $columnsValues 	= $dbObject->escapeValues($columnsValues);
			$attributePairs = static::generateKeyValue($columnsValues);
			if (!static::isExpressionValid($expression)) $expression = "AND";
			$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
			$sqlQuery 	   .= join(" $expression ", $attributePairs)." LIMIT 1";
			$resultArray 	= static::findBySql($sqlQuery);
			return !empty($resultArray) ? array_shift($resultArray) : FALSE;
		}

		/**
		* Find where a given column is equal to a given value.
		* @param string $column
		* @param string $value
		* @param string $order
		* @return array
		*/
		public static function findWhere (string $column, string $value, string $order="DESC") : array
		{
            $dbObject  = Database::getInstance();
			$order 	   = static::validateOrder($order);
			$sqlQuery  = "SELECT * FROM ".static::$tableName." WHERE ";
			$sqlQuery .= $dbObject->escapeValue($column)." = '";
			$sqlQuery .= $dbObject->escapeValue($value)."' ORDER BY id {$order}";
			return static::findBySql($sqlQuery);
		}

		/**
		* Find where a given column is equal to a given value (Multiple Columns).
		* $columnsValues takes an assoc array.
		* @param array $columnsValues
		* @param string $expression
		* @param string $order
		* @return array
		*/
		public static function findMultipleWhere (array $columnsValues, string $expression="AND", string $order="DESC") : array
		{
            $dbObject  		= Database::getInstance();
			$order 	   		= static::validateOrder($order);
            $columnsValues 	= $dbObject->escapeValues($columnsValues);
			$attributePairs = static::generateKeyValue($columnsValues);
			if (!static::isExpressionValid($expression)) $expression = "AND";
			$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
			$sqlQuery 	   .= join(" $expression ", $attributePairs);
			$sqlQuery      .= " ORDER BY id {$order}";
			return static::findBySql($sqlQuery);
		}
        
        /**
		* Find where a given column is equal to a given value (Multiple Columns).
		* $columnsValues takes an assoc array.
		* @param array $columnsValues
		* @param string $expression
		* @param string $order
		* @return array
		*/
		public static function findMultipleWhereBetween (array $columnsValues, string $expression="AND", string $order="DESC") : array
		{
            $dbObject  		= Database::getInstance();
			$order 	   		= static::validateOrder($order);
            $columnsValues 	= $dbObject->escapeValues($columnsValues);
			$attributePairs = static::generateKeyValue($columnsValues); 
			if (!static::isExpressionValid($expression)) $expression = "AND";
			$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
			$sqlQuery 	   .= join(" $expression ", $attributePairs);
			$sqlQuery      .= " ORDER BY id {$order}";
			return static::findBySql($sqlQuery);
		}

		/**
		* Count all database entries
		* @return int
		*/
		public static function countAll () : int
		{
            $dbObject  	= Database::getInstance();
			$sqlQuery 	= "SELECT COUNT(*) FROM ".static::$tableName;
			$resultSet 	= $dbObject->query($sqlQuery);
			$row 		= Database::fetchAssoc($resultSet);
			return (int) array_shift($row);
		}

		/**
		* Count where a given column is equal to a given value
		* @param string $column
		* @param string $value
		* @param string $expression
		* @return int
		*/
		public static function countWhere (string $column, string $value, string $expression="=") : int
		{
            $dbObject  	= Database::getInstance();
			$sqlQuery 	= "SELECT COUNT(*) as count FROM ".static::$tableName." WHERE ";
			$sqlQuery  .= $dbObject->escapeValue($column)." $expression '";
			$sqlQuery  .= $dbObject->escapeValue($value)."'";
			$resultSet 	= $dbObject->query($sqlQuery);
			$row 		= Database::fetchAssoc($resultSet);
			return (int) array_shift($row);
		}

		/**
		* Count where a given column is equal to a given value (Multiple Columns).
		* $columnsValues takes an assoc array.
		* @param array $columnsValues
		* @param string $expression
		* @return int
		*/
		public static function countMultipleWhere (array $columnsValues, string $expression="AND") : int
		{
            $dbObject  			= Database::getInstance();
            $columnsValues 		= $dbObject->escapeValues($columnsValues);
			$attributePairs 	= static::generateKeyValue($columnsValues);
			$sqlQuery 			= "SELECT COUNT(*) as count FROM ".static::$tableName." WHERE ";
			$sqlQuery 	   		.= join(" $expression ", $attributePairs);
			$resultSet 			= $dbObject->query($sqlQuery);
			$row 				= Database::fetchAssoc($resultSet);
			return (int) array_shift($row);
		}

		/**
		* Deletes a row from the database.
		* @param int $id
		* @return bool
		*/
		public function deleteRow (int $id) : bool
		{
			$this->id = $id;
			return $this->delete() ? TRUE : FALSE;
		}

		/**
		* If first property is empty it returns the second one.
		* @param mixed $firstProperty
		* @param mixed $secondProperty
		* @return mixed
		*/
		public static function checkProperty ($firstProperty='', $secondProperty='')
		{
			return Validate::hasValue($firstProperty) ? $firstProperty : $secondProperty;
		}
	}