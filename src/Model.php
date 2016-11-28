<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 5:44 PM
 */

namespace Framework;

use Traversable;

class Model implements ModelInterface, \JsonSerializable , \IteratorAggregate {
	
	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 * @since 5.0.0
	 */
	public function getIterator()
	{
		// TODO: Implement getIterator() method.
	}
	
	/**
	 * Get a property by name.
	 *
	 * @param $prop
	 *
	 * @return mixed
	 */
	public function get( $prop )
	{
		// TODO: Implement get() method.
	}
	
	/**
	 * Get all the object data as an associative array.
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		// TODO: Implement getAll() method.
	}
	
	/**
	 * Set a property by name.
	 *
	 * @param $prop
	 * @param $value
	 *
	 * @return static
	 */
	public function set( $prop, $value )
	{
		// TODO: Implement set() method.
	}
	
	/**
	 * Initialize all the data on the model.
	 *
	 * @param array $data
	 *
	 * @return static
	 */
	public function setAll( array $data = [] )
	{
		// TODO: Implement setAll() method.
	}
	
	/**
	 * Set an array of data on the model merging it with existing attributes.
	 *
	 * @param array $data
	 *
	 * @return static
	 */
	public function mergeData( array $data )
	{
		// TODO: Implement mergeData() method.
	}
	
	/**
	 * @param $prop
	 * @param $value
	 *
	 * @return mixed
	 */
	public function parse( $prop, $value )
	{
		// TODO: Implement parse() method.
	}
	
	/**
	 * @param      $json
	 * @param bool $parseAsArray
	 *
	 * @return array|object
	 */
	public function parseJSON( $json, $parseAsArray = TRUE )
	{
		// TODO: Implement parseJSON() method.
	}
	
	/**
	 * @param $boolean
	 *
	 * @return bool
	 */
	public function parseBool( $boolean )
	{
		// TODO: Implement parseBool() method.
	}
	
	/**
	 * @param $int
	 *
	 * @return int
	 */
	public function parseInt( $int )
	{
		// TODO: Implement parseInt() method.
	}
	
	/**
	 * @param $float
	 *
	 * @return float
	 */
	public function parseFloat( $float )
	{
		// TODO: Implement parseFloat() method.
	}
	
	/**
	 * Fetch a model from the database by id.
	 *
	 * @param $id
	 *
	 * @return static
	 */
	public function fetch( $id )
	{
		// TODO: Implement fetch() method.
	}
	
	/**
	 * Fetch a model from the database using a where clause.
	 * Method appends "LIMIT 1" to underlying query.
	 *
	 * @param $whereClause
	 *
	 * @return static
	 */
	public function fetchWhere( $whereClause )
	{
		// TODO: Implement fetchWhere() method.
	}
	
	/**
	 * Fetches an array of models from the database using a supplied where clause.
	 *
	 * @param $whereClause
	 *
	 * @return static[]
	 */
	public function fetchMany( $whereClause )
	{
		// TODO: Implement fetchMany() method.
	}
	
	/**
	 * Inserts the model in the database and returns the insert id.
	 * Should also check for the static::CREATED_AT field's presence,
	 * and timestamp a value if not present.
	 *
	 * @return int
	 */
	public function create()
	{
		// TODO: Implement create() method.
	}
	
	/**
	 * Intelligently inserts or updates the model based on
	 * whether or not an id is present in the model data.
	 *
	 * @return int
	 */
	public function save()
	{
		// TODO: Implement save() method.
	}
	
	/**
	 * Delete a model from the database (requires id to be present).
	 * If $softDelete is TRUE, updates the $softDeleteFieldName.
	 *
	 * @return bool|static
	 */
	public function delete()
	{
		// TODO: Implement delete() method.
	}
	
	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		// TODO: Implement jsonSerialize() method.
	}
}