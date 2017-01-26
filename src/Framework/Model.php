<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 5:44 PM
 */

namespace Framework;

use ArrayAccess;
use Database\MySQL;
use Framework\Exceptions\DirectAccessException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class Model
 * @package Framework
 */
abstract class Model implements ModelInterface, IteratorAggregate, JsonSerializable, ArrayAccess, \Countable {
	
	const CAST_FROM_JSON_TO_ARRAY  = 'CAST_FROM_JSON_TO_ARRAY';
	const CAST_FROM_JSON_TO_OBJECT = 'CAST_FROM_JSON_TO_OBJECT';
	const CAST_TO_INT              = 'CAST_TO_INT';
	const CAST_TO_FLOAT            = 'CAST_TO_FLOAT';
	const CAST_TO_PRICE            = 'CAST_TO_PRICE';
	const CAST_TO_BOOL             = 'CAST_TO_BOOL';
	
	/**
	 * Use this constant to define the model's database table
	 */
	const TABLE = __CLASS__;
	
	/**
	 * The underlying array of model data
	 * @var array
	 */
	protected $_data = [];
	
	/**
	 * Define as true if model should not be deleted from database.
	 * @var bool
	 */
	protected $useSoftDeletes = FALSE;
	
	/**
	 * Define with name of soft-delete field in database
	 * if $this->useSoftDeletes === TRUE
	 * @var string
	 */
	protected $softDeleteFieldName = 'deleted';
	
	/**
	 * Define with name of createdAt timestamp field
	 * @var string
	 */
	protected $timestampFieldName = 'created';
	
	/**
	 * If true, enforces the $timestampFieldName has a value when calling create()
	 * @var bool
	 */
	protected $timestamp = TRUE;
	
	/**
	 * An associative array of properties with casting instructions,
	 * the values of which are constants on the base class.
	 * @var array
	 */
	protected $casts = [
		'id' => self::CAST_TO_INT,
	];
	
	/**
	 * Model constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data = [] )
	{
		// The container connects only to a MySQL database,
		// so we can be sure this field should be cast to an integer,
		// but we'll leave this flexible to be overwritten in an extended class
		$this->casts = array_merge( [ 'id' => static::CAST_TO_INT, ], $this->casts );
		
		// Initialize the data on the model
		$this->setAll( $data );
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
		return isset( $this->_data[ $prop ] ) ? $this->_data[ $prop ] : NULL;
	}
	
	/**
	 * Get all the object data as an associative array.
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		return $this->_data;
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
		$this->_data[ $prop ] = $this->parse( $prop, $value );
		
		return $this;
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
		$this->_data = [];
		
		foreach ( $data as $prop => $value ) $this->set( $prop, $value );
		
		return $this;
	}
	
	/**
	 * @param $prop
	 *
	 * @return static
	 */
	public function remove( $prop )
	{
		unset( $this->_data[ $prop ] );
		
		return $this;
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
		return $this->setAll(
			array_merge( $this->getAll(), $data )
		);
	}
	
	/**
	 * Returns whether or not any data is set on the model
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty( $this->getAll() );
	}
	
	/**
	 * Fetch the model fields from the database,
	 * and remove all props on the model not in those fields.
	 * @return Model
	 */
	public function removePropsNotInDatabase()
	{
		$databaseProps = static::fetchDatabaseFields();
		
		return $this->setAll(
			Collection::instance( $this->getAll() )->filter( function ( $value, $prop ) use ( $databaseProps )
			{
				return in_array( $prop, $databaseProps );
			} )->toArray()
		);
	}
	
	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->getAll() );
	}
	
	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset )
	{
		return array_key_exists( $offset, $this->getAll() );
	}
	
	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 */
	public function offsetGet( $offset )
	{
		return $this->get( $offset );
	}
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset, $value )
	{
		$this->set( $offset, $value );
	}
	
	/**
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset )
	{
		$this->remove( $offset );
	}
	
	/**
	 * @param $prop
	 * @param $value
	 *
	 * @return mixed
	 */
	public function parse( $prop, $value )
	{
		if ( ! array_key_exists( $prop, $this->casts ) ) return $value;
		
		switch ( $this->casts[ $prop ] )
		{
			case static::CAST_FROM_JSON_TO_ARRAY:
				return $this->parseJSON( $value, TRUE );
			
			case static::CAST_FROM_JSON_TO_OBJECT:
				return $this->parseJSON( $value, FALSE );
			
			case static::CAST_TO_BOOL:
				return $this->parseBool( $value );
			
			case static::CAST_TO_FLOAT:
				return $this->parseFloat( $value );
			
			case static::CAST_TO_PRICE:
				return $this->parsePrice( $value );
			
			case static::CAST_TO_INT:
				return $this->parseInt( $value );
			
			default:
				return $value;
		}
	}
	
	/**
	 * @param      $json
	 * @param bool $parseAsArray
	 *
	 * @return array|object
	 */
	public function parseJSON( $json, $parseAsArray = TRUE )
	{
		if ( ! $parseAsArray )
		{
			while ( gettype( $json ) == 'string' ) $json = json_decode( $json );
			
			return $json;
		}
		
		while ( gettype( $json ) == 'string' ) $json = json_decode( $json, TRUE );
		
		if ( is_null( $json ) ) $json = [];
		
		if ( ! is_array( $json ) )
		{
			$type = gettype( $json );
			throw new \InvalidArgumentException(
				"Could not parse JSON to array. Value: [{$json}] Type: [{$type}]"
			);
		}
		
		return $json;
	}
	
	/**
	 * @param $boolean
	 *
	 * @return bool
	 */
	public function parseBool( $boolean )
	{
		return (bool) $boolean;
	}
	
	/**
	 * @param $int
	 *
	 * @return int
	 */
	public function parseInt( $int )
	{
		return (int) $int;
	}
	
	/**
	 * @param $float
	 *
	 * @return float
	 */
	public function parseFloat( $float )
	{
		return (float) $float;
	}
	
	/**
	 * @param $price
	 *
	 * @return float
	 */
	public function parsePrice( $price )
	{
		return round( (float) $price, 2 );
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
		if ( $this->timestamp && empty( $this->get( $this->timestampFieldName ) ) )
		{
			$this->set( $this->timestampFieldName, MySQL::now() );
		}
		
		$this->set(
			'id',
			$this->parseInt(
				Container::db()->insert( static::TABLE, $this->getAll(), TRUE )
			)
		);
		
		return $this->get( 'id' );
	}
	
	/**
	 * Intelligently inserts or updates the model based on
	 * whether or not an id is present in the model data.
	 *
	 * @return int
	 */
	public function save()
	{
		if ( $id = $this->parseInt( $this->get( 'id' ) ) )
		{
			Container::db()->update( static::TABLE, $this->getAll(), $id );
			
			return $id;
		}
		else
		{
			return $this->create();
		}
	}
	
	/**
	 * Delete a model from the database (requires id to be present).
	 * If $useSoftDeletes is TRUE, updates the $softDeleteFieldName.
	 *
	 * @return bool|ModelInterface
	 */
	public function delete()
	{
		if ( $this->useSoftDeletes ) return $this->softDelete();
		
		return $this->parseBool(
			Container::db()->delete(
				static::TABLE,
				$this->parseInt( $this->get( 'id' ) )
			)
		);
	}
	
	/**
	 * @return static
	 */
	public function softDelete()
	{
		Container::db()->update(
			static::TABLE,
			[ $this->softDeleteFieldName => TRUE ],
			$this->parseInt( $this->get( 'id' ) )
		);
		
		return $this;
	}
	
	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return array data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return $this->getAll();
	}
	
	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->jsonSerialize();
	}
	
	/**
	 * @param int $options
	 * @param int $depth
	 *
	 * @return string
	 */
	public function toJson( $options = 0, $depth = 512 )
	{
		return json_encode( $this, $options, $depth );
	}
	
	/**
	 * @return string
	 */
	public function getClass()
	{
		return static::getNamespace();
	}
	
	/**
	 * @return string
	 */
	public function getFullyQualifiedClass()
	{
		return static::getFullyQualifiedNamespace();
	}
	
	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 * @since 5.0.0
	 */
	public function getIterator()
	{
		return new \ArrayIterator( $this->_data );
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}
	
	/**
	 * @param $prop
	 * @param $value
	 *
	 * @throws DirectAccessException
	 */
	public function __set( $prop, $value )
	{
		$className = static::getNamespace();
		
		throw new DirectAccessException(
			"Cannot set property [{$prop} = {$value}] directly on {$className}. Use set method."
		);
	}
	
	/**
	 * @param $prop
	 *
	 * @throws DirectAccessException
	 */
	public function __get( $prop )
	{
		$className = static::getNamespace();
		
		throw new DirectAccessException(
			"Cannot access property [{$prop}] directly on {$className}. Use get method."
		);
	}
	
	/**
	 * @param array $data
	 *
	 * @return static
	 */
	public static function instance( array $data = [] )
	{
		return new static( $data );
	}
	
	/**
	 * Returns an array of field names from the connected database.
	 *
	 * @return array
	 */
	public static function fetchDatabaseFields()
	{
		return Container::db()->getColumns( static::TABLE );
	}
	
	/**
	 * Fetch a model from the database by id.
	 *
	 * @param $id
	 *
	 * @return static
	 */
	public static function fetch( $id )
	{
		$instance = static::instance();
		
		Container::db()
		         ->select( static::TABLE, (int) $id )
		         ->iterateResult( function ( array $modelData ) use ( $instance )
		         {
			         $instance->setAll( $modelData );
		         } );
		
		return $instance;
	}
	
	/**
	 * Fetch a model from the database using a where clause.
	 * Method appends "LIMIT 1" to underlying query.
	 *
	 * @param $whereClause
	 *
	 * @return static
	 */
	public static function fetchWhere( $whereClause )
	{
		$instance = static::instance();
		
		Container::db()
		         ->select( static::TABLE, [ '*' ], "{$whereClause} LIMIT 1" )
		         ->iterateResult( function ( array $modelData ) use ( $instance )
		         {
			         $instance->setAll( $modelData );
		         } );
		
		return $instance;
	}
	
	/**
	 * Fetches an array of models from the database using a supplied where clause.
	 *
	 * @param $whereClause
	 *
	 * @return CollectionInterface[static]
	 */
	public static function fetchMany( $whereClause = '' )
	{
		return
			Collection::instance(
				Container::db()
				         ->select( static::TABLE, [ '*' ], $whereClause )
				         ->mapResult( function ( array $modelData )
				         {
					         return static::instance( $modelData );
				         } )
			);
	}
	
	/**
	 * Gets the fully-qualified class name of the late-statically bound class
	 *
	 * @return string
	 */
	public static function getFullyQualifiedNamespace()
	{
		return substr( static::class, 0, 1 ) === '\\' ? static::class : '\\' . static::class;
	}
	
	/**
	 * @return string
	 */
	public static function getNamespace()
	{
		return static::class;
	}
}