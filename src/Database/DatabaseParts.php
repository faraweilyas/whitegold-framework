<?php

namespace Blaze\Database;


/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link https://faraweilyas.com
*
* DatabaseParts Class
*/
class DatabaseParts
{
	/**
	* Returns each row as objects in an associative array
	* @param string $sql
	* @return array
	*/
	final public static function findBySql(string $sql) : array
	{
        $dbObject 		= Database::getInstance();
		$resultSet 		= $dbObject->query($sql);
		$objectArray 	= [];
		while($row = Database::fetchAssoc($resultSet))
		{
			$objectArray[] = static::instantiate($row);
		}
		return $objectArray;
	}
	
	/**
	* Instantiates a new record 
	* @param array $record
	* @return object
	*/
	final public static function instantiate(array $record)
	{
		$className = get_called_class();
		return new $className($record);
	}

	/**
	* Checks if returned resultArray should return 1 element.
	* @param string $sqlQuery
	* @param int $limit
	* @return mixed
	*/
	final public static function limitFindBySql (string $sqlQuery, int $limit=0)
	{
		$resultArray = static::findBySql($sqlQuery.static::limitQuery($limit));
		if (empty($resultArray)) return FALSE;
		return ($limit == 1) ? array_shift($resultArray) : $resultArray;
	}

	/**
	* Generates limit query.
	* @param int $limit
	* @return string
	*/
	final public static function limitQuery (int $limit=0) : string
	{
		return ($limit > 0) ? " LIMIT {$limit}" : "";
	}

	/**
	* Return an array of attribute names and their values.
	* @return array
	*/
	final public function attributes () : array
	{
		$attributes = [];
		foreach (static::$databaseFields as $field):
			if (isset($this->$field))
				$attributes[$field] = $this->$field;
		endforeach;
		return $attributes;
	}

	/**
	* Sanitize the values before submitting
	* Note: does not alter the actual value of each attribute
	* @return array
	*/
	final public function sanitizedAttributes () : array
	{
        $dbObject 			= Database::getInstance();
		$cleanAttributes 	= [];
		foreach ($this->attributes() as $key => $value):
			if (isset($value))
				$cleanAttributes[$key] = $dbObject->escapeValue($value);
		endforeach;
		return $cleanAttributes;
	}

	/**
	* Generating sql query for update
	* @param array
	* @return string
	*/
	final public function generateQueryForUpdate () : string
	{
		$generatedArray = [];
		foreach ($this->sanitizedAttributes() as $key => $value):
			$generatedArray[] = "{$key} = '{$value}'";
		endforeach;
		return joinArray($generatedArray, ", ");
	}

	/**
	* Checks if an object has given attribute.
	* @param string $attribute
	* @return bool
	*/
	final public function hasAttribute (string $attribute) : bool
	{
		$objectAttributes = $this->attributes();
		return array_key_exists($attribute, $objectAttributes);
	}

	/**
	* Validates the order for sqlQuery
	* @param string $order
	* @return string
	*/
	final protected static function validateOrder (string $order="DESC") : string
	{
		$order = strtoupper($order);
		return (!in_array($order, ["DESC", "ASC"])) ? "DESC" : $order;
	}

	/**
	* Generates keys and thier values for sqlQuery
	* @param array $associativeArray
	* @param string $expressionOperator
	* @return array
	*/
	final protected static function generateKeyValue (array $associativeArray, string $expressionOperator="AND") : array
	{
		$generatedArray = [];
        $dbObject 		= Database::getInstance();
		foreach ($associativeArray as $column => $operatorValues):
	        $column = $dbObject->escapeValue($column);
			$generatedArray[] = "{$column} ".static::generateValue($column, $operatorValues, $expressionOperator);
		endforeach;
		return $generatedArray;
	}

	/**
	* Generate values for sqlQuery based on the operator
	* @param string $column
	* @param array $operatorValues
	* @param string $expressionOperator
	* @return string
	*/
	final protected static function generateValue (string $column, array $operatorValues, string $expressionOperator="AND") : string
	{
        $dbObject 			= Database::getInstance();
        $column 			= $dbObject->escapeValue($column);
		$operator 			= static::validateOperator(array_shift($operatorValues));
        $values 			= $dbObject->escapeValues($operatorValues);
		$expressionOperator = static::validateOperator($expressionOperator, "AND");
		$expression 		= "";
		if (count($values) > 1):
			switch ($operator)
			{
				case 'BETWEEN':
					$expression = joinArray($values, "' AND '", "{$operator} '", "'");
					break;
				case 'IN':
					$expression = joinArray($values, "', '", "{$operator} ('", "')");
					break;
				case '=':
				case '<':
				case '<=':
				case '>':
				case '>=':
				case '<>':
				default:
					$expression = joinArray($values, "' {$expressionOperator} {$column} {$operator} '", "{$operator} '", "'");
					break;
			}
		else:
	        $expression = "{$operator} '".$dbObject->escapeValue($values[0])."'";
		endif;
		return $expression;
	}

	/**
	* Checks if expression is valid.
	* @param string $expression
	* @param string $defaultExpression
	* @return bool
	*/
	final protected static function isExpressionValid (string $expression, string $defaultExpression="=") : bool
	{
		return static::validateOperator($expression, $defaultExpression);
	}

	/**
	* Validates operator.
	* @param string $operator
	* @param string $defaultOperator
	* @return string
	*/
	final protected static function validateOperator (string $operator, string $defaultOperator="=") : string
	{
		return static::isOperatorValid($operator) ? $operator : ($defaultOperator ?: '=');
	}

	/**
	* Checks if operator is valid.
	* @param string $operator
	* @return bool
	*/
	final protected static function isOperatorValid (string $operator) : bool
	{
		// Arithmetic Operators
		$operators1 = ['+', '-', '*', '/', '%'];
		// Bitwise Operators
		$operators2 = ['&', '|', '^'];
		// Comparison Operators
		$operators3 = ['=', '<', '>', '>=', '<=', '<>'];
		// Compound Operators
		$operators4 = ['+=', '-=', '*=', '/=', '%=', '&=', '^-=', '|*='];
		// Logical Operators
		$operators5 = ['ALL', 'AND', 'ANY', 'BETWEEN', 'EXISTS', 'IN', 'LIKE', 'NOT', 'OR', 'SOME'];
		$operators 	= array_merge($operators1, $operators2, $operators3, $operators4, $operators5);
		return in_array(strtoupper($operator), $operators);
	}
}
