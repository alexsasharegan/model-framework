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
	 * @return static
	 */
	public function set( $prop, $value );
	
	/**
	 * Initialize all the data on the model.
	 *
	 * @param array $data
	 *
	 * @return static
	 */
	public function setAll( array $data = [] );
	
	/**
	 * Set an array of data on the model merging it with existing attributes.
	 *
	 * @param array $data
	 *
	 * @return static
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
	 * Fetch a model from the database by id.
	 *
	 * @param $id
	 *
	 * @return static
	 */
	public function fetch( $id );
	
	/**
	 * Fetch a model from the database using a where clause.
	 * Method appends "LIMIT 1" to underlying query.
	 *
	 * @param $whereClause
	 *
	 * @return static
	 */
	public function fetchWhere( $whereClause );
	
	/**
	 * Fetches an array of models from the database using a supplied where clause.
	 *
	 * @param $whereClause
	 *
	 * @return static[]
	 */
	public function fetchMany( $whereClause );
	
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
	 * If $softDelete is TRUE, updates the $softDeleteFieldName.
	 *
	 * @return bool|static
	 */
	public function delete();
	
}