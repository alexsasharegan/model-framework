<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 5:46 PM
 */

namespace Framework;

interface ModelInterface {
	
	/**
	 * Get a property by name.
	 *
	 * @param $prop
	 *
	 * @return mixed
	 */
	public function get( $prop );
	
	/**
	 * Get all the object data as an associative array.
	 *
	 * @return mixed
	 */
	public function getAll();
	
	/**
	 * Set a property by name.
	 *
	 * @param $prop
	 * @param $value
	 *
	 * @return ModelInterface
	 */
	public function set( $prop, $value );
	
	/**
	 * Initialize all the data on the model.
	 *
	 * @param array $data
	 *
	 * @return ModelInterface
	 */
	public function setAll( array $data = [] );
	
	/**
	 * Unset a property on the model.
	 *
	 * @param $prop
	 *
	 * @return ModelInterface
	 */
	public function remove( $prop );
	
	/**
	 * Set an array of data on the model merging it with existing attributes.
	 *
	 * @param array $data
	 *
	 * @return ModelInterface
	 */
	public function mergeData( array $data );
	
	/**
	 * @param $prop
	 * @param $value
	 *
	 * @return mixed
	 */
	public function parse( $prop, $value );
	
	/**
	 * @param      $json
	 * @param bool $parseAsArray
	 *
	 * @return array|object
	 */
	public function parseJSON( $json, $parseAsArray = TRUE );
	
	/**
	 * @param $boolean
	 *
	 * @return bool
	 */
	public function parseBool( $boolean );
	
	/**
	 * @param $int
	 *
	 * @return int
	 */
	public function parseInt( $int );
	
	/**
	 * @param $float
	 *
	 * @return float
	 */
	public function parseFloat( $float );
	
	/**
	 * @param $price
	 *
	 * @return float
	 */
	public function parsePrice( $price );
	
	/**
	 * Fetch a model from the database by id.
	 *
	 * @param $id
	 *
	 * @return ModelInterface
	 */
	public static function fetch( $id );
	
	/**
	 * Fetch a model from the database using a where clause.
	 * Method appends "LIMIT 1" to underlying query.
	 *
	 * @param $whereClause
	 *
	 * @return ModelInterface
	 */
	public static function fetchWhere( $whereClause );
	
	/**
	 * Fetches an array of models from the database using a supplied where clause.
	 *
	 * @param $whereClause
	 *
	 * @return ModelInterface[]
	 */
	public static function fetchMany( $whereClause );
	
	/**
	 * Inserts the model in the database and returns the insert id.
	 * Should also check for the static::CREATED_AT field's presence,
	 * and timestamp a value if not present.
	 *
	 * @return int
	 */
	public function create();
	
	/**
	 * Intelligently inserts or updates the model based on
	 * whether or not an id is present in the model data.
	 *
	 * @return int
	 */
	public function save();
	
	/**
	 * Delete a model from the database (requires id to be present).
	 * If $useSoftDeletes is TRUE, updates the $softDeleteFieldName.
	 *
	 * @return bool|ModelInterface
	 */
	public function delete();
	
	/**
	 * @return ModelInterface
	 */
	public function softDelete();
	
	/**
	 * @return string
	 */
	public function toJson();
	
	/**
	 * @return ModelInterface
	 */
	public static function instance();
	
}