<?php

namespace Blaze\Database;

use Blaze\Validation\Validator as Validate;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link https://faraweilyas.com
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
	* Stores the affected rows
	* @var int
	*/
	protected static $affectedRows 		= 0;

	/**
	* Stores the found database rows as an object.
	* @var mixed
	*/
	protected static $foundObject;

	/**
	* Stores the found database rows as an array.
	* @var array
	*/
    protected $record;

	/**
	* Constructor to set record upon initialization.
	* @param array $record
	* @return void
	*/
    public function __construct(array $record=[])
    {
        $this->record = $record;
    }

    /**
    * Returns database table name property
    * @return string
    */
	public static function getTableName() : string
	{
		return static::$tableName;
	}

    /**
    * Returns database fields property
    * @return array
    */
	public static function getDatabaseFields() : array
	{
		return static::$databaseFields;
	}

    /**
    * Returns foundObject property
    * @return mixed
    */
	public static function getFoundObject()
	{
		return static::$foundObject;
	}

	/**
	* Magic get method.
	* @param string $property
	* @return mixed
	*/
    public function __get(string $property)
    {
    	if (array_key_exists($property, $this->record))
    		return $this->record[$property];
    	if (property_exists($this, $property))
    		return $this->$property;
		return NULL;
    }

	/**
	 * Get method to get record.
	 * @return $this
	 */
    public function get()
    {
		$className 	= get_called_class();
		$object 	= new $className;
		foreach ($this->record as $attribute => $value):
			$object->$attribute = $value;
		endforeach;
		return $object;
    }

	/**
	* Update or Create new records.
	* A new record won't have an id yet.
	* @return bool
	*/
	public function save() : bool
	{
		return isset($this->id) ? $this->update() : $this->create();
	}

	/**
	* Create new record in the database.
	* @return bool
	*/
	public function create() : bool
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
	public function update() : bool
	{
        $dbObject 	= Database::getInstance();
		$sqlQuery 	= "UPDATE ".static::$tableName." SET ".$this->generateQueryForUpdate();
		$sqlQuery  .= " WHERE id = ". $dbObject->escapeValue($this->id);
		$result 	= $dbObject->query($sqlQuery);
    	return ($result) ? TRUE : FALSE;
	}

	/**
	* Deletes a row from the database.
	* @param int $id
	* @return bool
	*/
	public function deleteRow(int $id) : bool
	{
		$this->id = $id;
		return $this->delete() ? TRUE : FALSE;
	}

	/**
	* Delete a record from the database
	* @return bool
	*/
	public function delete() : bool
	{
        $dbObject  = Database::getInstance();
		$sqlQuery  = "DELETE FROM ".static::$tableName." WHERE id=";
		$sqlQuery .= $dbObject->escapeValue($this->id)." LIMIT 1";
		$dbObject->query($sqlQuery);
		static::$affectedRows = $dbObject->affectedRows();
		return ($dbObject->affectedRows() == 1) ? TRUE : FALSE;
	}

	/**
	* Delete a record from the database where a given column is equal to the given value 
	* @param string $column
	* @param array $operatorValue
	* @param string $joinOperator
	* @return bool
	*/
	public static function deleteWhere(string $column, array $operatorValue, string $joinOperator="AND") : bool
	{
		$joinOperator 	= static::validateOperator($joinOperator, "AND");
		$expression 	= static::generateValue($column, $operatorValue, $joinOperator);
		$sqlQuery  		= "DELETE FROM ".static::$tableName." WHERE {$column} {$expression}";
        $dbObject  		= Database::getInstance();
		$dbObject->query($sqlQuery);
		static::$affectedRows = $dbObject->affectedRows();
		return ($dbObject->affectedRows() > 0) ? TRUE : FALSE;
	}

	/**
	* Delete where a given column or more is equal to a given value (Multiple Columns).
	* @param array $columnsOperatorsValues takes an assoc array and first value should be the expression
	* @param string $joinOperator
	* @param string $expressionOperator
	* @return bool
	*/
	public static function deleteMultipleWhere(array $columnsOperatorsValues, string $joinOperator="AND", string $expressionOperator="AND") : bool
	{
		$joinOperator 	= static::validateOperator($joinOperator, "AND");
		$expressions 	= static::generateKeyValue($columnsOperatorsValues, $expressionOperator);
		$sqlQuery  		= "DELETE FROM ".static::$tableName." WHERE ".join(" $joinOperator ", $expressions);
        $dbObject  		= Database::getInstance();
		$dbObject->query($sqlQuery);
		static::$affectedRows = $dbObject->affectedRows();
		return ($dbObject->affectedRows() > 0) ? TRUE : FALSE;
	}

	/**
	* Find all from the database
	* @param string $order
	* @return array
	*/
	public static function findAll(string $order="DESC") : array
	{
		$order = static::validateOrder($order);
		return static::findBySql("SELECT * FROM ".static::$tableName." ORDER BY id {$order}");
	}

	/**
	* Finds a row or more rows by the id column and returns found row as an object otherwise FALSE
	* @param int $id
	* @param string $operator
	* @param int $limit
	* @return mixed
	*/
	public static function findById(int $id, string $operator="=", int $limit=1)
	{
        $id 		= Database::getInstance()->escapeValue($id);
		$operator 	= static::validateOperator($operator, '=');
		return static::limitFindBySql("SELECT * FROM ".static::$tableName." WHERE id {$operator} {$id}", $limit);
	}

	/**
	* Find some of the database rows by limiting it.
	* @param int $limit
	* @param string $order
	* @return mixed
	*/
	public static function findSome(int $limit=0, string $order="DESC")
	{
		$order = static::validateOrder($order);
		return static::limitFindBySql("SELECT * FROM ".static::$tableName." ORDER BY id {$order}", $limit);
	}

	/**
	* Find by column from the database
	* @param string $column
	* @param int $limit
	* @param string $order
	* @return mixed
	*/
	public static function findColumn(string $column, int $limit=0, string $order="DESC")
	{
		$order 		= static::validateOrder($order);
        $dbObject 	= Database::getInstance();
        $column 	= $dbObject->escapeValue($column);
		return static::limitFindBySql("SELECT {$column} FROM ".static::$tableName." ORDER BY id {$order}", $limit);
	}

	/**
	* Find by columns from the database
	* @param string $columns
	* @param int $limit
	* @param string $order
	* @return mixed
	*/
	public static function findColumns(array $columns, int $limit=0, string $order="DESC")
	{
		$order 	   	= static::validateOrder($order);
        $dbObject  	= Database::getInstance();
		$columns 	= joinArray($dbObject->escapeValues($columns), ', ');
		return static::limitFindBySql("SELECT {$columns} FROM ".static::$tableName." ORDER BY id {$order}", $limit);
	}

	/**
	* Find where a given column is equal to a given value and return the first occurence as the first element in the array.
	* @param string $column
	* @param array $operatorValue
	* @param string $joinOperator
	* @param int $limit
	* @return mixed
	*/
	public static function findByColumn(string $column, array $operatorValue, string $joinOperator="AND", int $limit=1)
	{
		$joinOperator 	= static::validateOperator($joinOperator, "AND");
		$expression 	= static::generateValue($column, $operatorValue, $joinOperator);
		$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE {$column} {$expression}";
		return static::limitFindBySql($sqlQuery, $limit);
	}

	/**
	* Find where a given column is equal to a given value and return the first occurence as the first element in the array (Multiple Columns).
	* @param array $columnsOperatorsValues takes an assoc array and first value should be the expression
	* @param string $joinOperator
	* @param string $expressionOperator
	* @param int $limit
	* @return mixed
	*/
	public static function findMultipleColumn(array $columnsOperatorsValues, string $joinOperator="AND", string $expressionOperator="AND", int $limit=1)
	{
		$joinOperator 	= static::validateOperator($joinOperator, 'AND');
		$expressions 	= static::generateKeyValue($columnsOperatorsValues, $expressionOperator);
		$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
		$sqlQuery 	   .= join(" $joinOperator ", $expressions);
		return static::limitFindBySql($sqlQuery, $limit);
	}

	/**
	* Find where a given column is equal to a given value.
	* @param string $column
	* @param array $operatorValue
	* @param string $order
	* @param string $joinOperator
	* @param int $limit
	* @return mixed
	*/
	public static function findWhere(string $column, array $operatorValue, string $order="DESC", string $joinOperator="AND", int $limit=0)
	{
		$joinOperator 	= static::validateOperator($joinOperator, "AND");
		$expression 	= static::generateValue($column, $operatorValue, $joinOperator);
		$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE {$column} {$expression} ORDER BY id {$order}";
		return static::limitFindBySql($sqlQuery, $limit);
	}

	/**
	* Find where a given column or more is equal to a given value (Multiple Columns).
	* @param array $columnsOperatorsValues takes an assoc array and first value should be the expression
	* @param string $joinOperator
	* @param string $order
	* @param string $expressionOperator
	* @param int $limit
	* @return mixed
	*/
	public static function findMultipleWhere(array $columnsOperatorsValues, string $joinOperator="AND", string $order="DESC", string $expressionOperator="AND", int $limit=0)
	{
		$joinOperator 	= static::validateOperator($joinOperator, 'AND');
		$expressions 	= static::generateKeyValue($columnsOperatorsValues, $expressionOperator);
		$sqlQuery  		= "SELECT * FROM ".static::$tableName." WHERE ";
		$sqlQuery 	   .= join(" $joinOperator ", $expressions)." ORDER BY id {$order}";
		return static::limitFindBySql($sqlQuery, $limit);
	}

	/**
	* Group by
	* @param string $field
	* @param string $order
	* @return array
	*/
	public static function groupBy(string $field, string $order="DESC") : array
	{
		$order = static::validateOrder($order);
		return static::findBySql("SELECT * FROM ".static::$tableName." GROUP BY {$field} ORDER BY id {$order}");
	}

	/**
	* Search the database
	* @param string $column
	* @param string $searchQ
	* @param int $limit
	* @param string $order
	* @return mixed
	*/
	public static function search(string $column, string $searchQ, int $limit=0, string $order="DESC")
	{
		$order = static::validateOrder($order);
		return static::limitFindBySql("SELECT * FROM ".static::$tableName." WHERE {$column} LIKE '%$searchQ%' ORDER BY id {$order}", $limit);
	}

	/**
	* Count all database entries
	* @return int
	*/
	public static function countAll() : int
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
	* @param array $operatorValue
	* @param string $joinOperator
	* @return int
	*/
	public static function countWhere(string $column, array $operatorValue, string $joinOperator="AND") : int
	{
		$joinOperator 	= static::validateOperator($joinOperator, "AND");
		$expression 	= static::generateValue($column, $operatorValue, $joinOperator);
		$sqlQuery  		= "SELECT COUNT(*) as count FROM ".static::$tableName." WHERE {$column} {$expression}";
		$resultSet 		= Database::getInstance()->query($sqlQuery);
		$row 			= Database::fetchAssoc($resultSet);
		return (int) array_shift($row);
	}

	/**
	* Count where a given column is equal to a given value (Multiple Columns).
	* @param array $columnsOperatorsValues takes an assoc array and first value should be the expression
	* @param string $joinOperator
	* @param string $expressionOperator
	* @return int
	*/
	public static function countMultipleWhere(array $columnsOperatorsValues, string $joinOperator="AND", string $expressionOperator="AND") : int
	{
		$joinOperator 	= static::validateOperator($joinOperator, 'AND');
		$expressions 	= static::generateKeyValue($columnsOperatorsValues, $expressionOperator);
		$sqlQuery  		= "SELECT COUNT(*) as count FROM ".static::$tableName." WHERE ".join(" $joinOperator ", $expressions);
		$resultSet 		= Database::getInstance()->query($sqlQuery);
		$row 			= Database::fetchAssoc($resultSet);
		return (int) array_shift($row);
	}

	/**
	* If first property is empty it returns the second one.
	* @param mixed $firstProperty
	* @param mixed $secondProperty
	* @return mixed
	*/
	public static function checkProperty($firstProperty='', $secondProperty='')
	{
		return Validate::hasValue($firstProperty) ? $firstProperty : $secondProperty;
	}
}
