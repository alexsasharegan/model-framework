<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 9:43 PM
 */

namespace Framework;

use Traversable;

class Collection implements CollectionInterface, \JsonSerializable, \ArrayAccess, \IteratorAggregate, \Countable {
	
	protected $_data = [];
	
	/**
	 * Collection constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data = [] )
	{
		$this->_data = $data;
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
	 * The all method returns the underlying array represented by the collection
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->_data;
	}
	
	/**
	 * The append method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function append( $item )
	{
		array_push( $this->_data, $item );
		
		return $this;
	}
	
	/**
	 * The at method returns the item at a given index in the collection
	 *
	 * @param int $index
	 *
	 * @return array
	 */
	public function at( $index )
	{
		settype( $index, 'integer' );
		
		return isset( $this->_data[ $index ] ) ? $this->_data[ $index ] : NULL;
	}
	
	/**
	 * The chunk method breaks the collection into multiple, smaller collections of a given size
	 *
	 * @param $chunkSize
	 *
	 * @return CollectionInterface
	 */
	public function chunk( $chunkSize )
	{
		settype( $chunkSize, 'integer' );
		$newCollection = static::instance();
		$chunk         = static::instance();
		
		for ( $i = 0; $i < $this->count(); $i++ )
		{
			$chunk->push( $this->at( $i ) );
			
			if ( ($i + 1) % $chunkSize === 0 )
			{
				$newCollection->push( $chunk );
				$chunk = static::instance();
			}
		}
		
		if ( ! $chunk->isEmpty() ) $newCollection->push( $chunk );
		
		return $newCollection;
	}
	
	/**
	 * The collapse method collapses a collection of arrays into a single, flat collection
	 *
	 * @return CollectionInterface
	 */
	public function collapse()
	{
		$newData = [];
		
		foreach ( $this->all() as $item )
		{
			if ( $item instanceof CollectionInterface )
			{
				$newData = array_merge( $newData, $item->collapse()->all() );
			}
			else
			{
				$newData[] = $item;
			}
		}
		
		return static::instance( $newData );
	}
	
	/**
	 * The count method returns the total number of items in the collection
	 *
	 * @return int
	 */
	public function count()
	{
		return count( $this->_data );
	}
	
	/**
	 * The each method iterates over the items in the collection and passes each item to a callback.
	 * Return FALSE to exit the loop.
	 *
	 * @param \Closure $f
	 * @param bool     $passByReference
	 *
	 * @return CollectionInterface
	 */
	public function each( \Closure $f, $passByReference = FALSE )
	{
		if ( $passByReference )
		{
			foreach ( $this->all() as $index => &$item )
			{
				if ( $f( $item, $index ) === FALSE ) break;
			}
		}
		else
		{
			foreach ( $this->all() as $index => $item )
			{
				if ( $f( $item, $index ) === FALSE ) break;
			}
		}
		
		return $this;
	}
	
	/**
	 * The filter method filters the collection using the given callback,
	 * keeping only those items that pass a given truth test
	 *
	 * If no callback is supplied,
	 * all entries of the collection that are equivalent to false will be removed
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function filter( \Closure $f )
	{
		$filtered = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( $f( $item, $index ) ) $filtered->set( $index, $item );
		}
		
		return $filtered;
	}
	
	/**
	 * The forPage method returns a new collection containing the items
	 * that would be present on a given page number.
	 * The method accepts the page number as its first argument
	 * and the number of items to show per page as its second argument
	 *
	 * @param $pageNumber
	 * @param $itemsPerPage
	 *
	 * @return CollectionInterface
	 */
	public function forPage( $pageNumber, $itemsPerPage )
	{
		settype( $pageNumber, 'integer' );
		settype( $itemsPerPage, 'integer' );
		
		$numberOfPages = ceil( $this->count() / $itemsPerPage );
		
		if ( $pageNumber > $numberOfPages ) return static::instance();
		
		$startIndex = $pageNumber <= 1 ? 0 : ($pageNumber - 1) * $itemsPerPage;
		
		return $this->slice( $startIndex, $itemsPerPage );
	}
	
	/**
	 * Gets a value from the collection by a given key.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get( $key )
	{
		return isset( $this->_data[ $key ] ) ? $this->_data[ $key ] : NULL;
	}
	
	/**
	 * The includes method passes each item in the collection to a callback
	 * and returns true at the first item that returns true from the callback, or false
	 *
	 * @param \Closure $f
	 *
	 * @return bool
	 */
	public function includes( \Closure $f )
	{
		foreach ( $this->all() as $index => $item )
		{
			if ( $f( $item, $index ) ) return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * The isEmpty method returns true if the collection is empty; otherwise, false is returned
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty( $this->_data );
	}
	
	/**
	 * The map method iterates through the collection
	 * and passes each value to the given callback.
	 * The callback is free to modify the item and return it,
	 * thus forming a new collection of modified items
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function map( \Closure $f )
	{
		$mapped = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			$mapped->set( $index, $f( $item, $index ) );
		}
		
		return $mapped;
	}
	
	/**
	 * The pop method removes and returns the last item from the collection
	 *
	 * @return mixed
	 */
	public function pop()
	{
		return array_pop( $this->_data );
	}
	
	/**
	 * The prepend method adds an item to the beginning of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function prepend( $item )
	{
		array_unshift( $this->_data, $item );
		
		return $this;
	}
	
	/**
	 * The push method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function push( $item )
	{
		array_push( $this->_data, $item );
		
		return $this;
	}
	
	/**
	 * The reduce method reduces the collection to a single value,
	 * passing the result of each iteration into the subsequent iteration
	 *
	 * The value for $carry on the first iteration is null;
	 * however, you may specify its initial value by passing a second argument to reduce
	 *
	 * @param \Closure $f
	 * @param null     $carry
	 *
	 * @return mixed
	 */
	public function reduce( \Closure $f, $carry = NULL )
	{
		foreach ( $this->all() as $index => $item )
		{
			$carry = $f( $carry, $item, $index );
		}
		
		return $carry;
	}
	
	/**
	 * The reject method filters the collection using the given callback.
	 * The callback should return true if the item should be removed from the resulting collection
	 *
	 * (opposite of filter)
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function reject( \Closure $f )
	{
		$rejected = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( ! $f( $item, $index ) ) $rejected->set( $index, $item );
		}
		
		return $rejected;
	}
	
	/**
	 * Removes an item from the collection at a given key
	 *
	 * @param $key
	 *
	 * @return CollectionInterface
	 */
	public function remove( $key )
	{
		unset( $this->_data[ $key ] );
		
		return $this;
	}
	
	/**
	 * The reverse method reverses the order of the collection's items
	 *
	 * @return CollectionInterface
	 */
	public function reverse()
	{
		return static::instance( array_reverse( $this->all() ) );
	}
	
	/**
	 * The set method sets a value by a given key on the collection
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return CollectionInterface
	 */
	public function set( $key, $value )
	{
		$this->_data[ $key ] = $value;
		
		return $this;
	}
	
	/**
	 * The shift method removes and returns the first item from the collection
	 *
	 * @return mixed
	 */
	public function shift()
	{
		return array_shift( $this->_data );
	}
	
	/**
	 * The slice method returns a slice of the collection starting at the given index
	 *
	 * @param int $startIndex
	 * @param     $length
	 *
	 * @return CollectionInterface
	 */
	public function slice( $startIndex = 0, $length = NULL )
	{
		if ( ! is_null( $length ) ) settype( $length, 'integer' );
		
		settype( $startIndex, 'integer' );
		
		return static::instance( array_slice( $this->all(), $startIndex, $length ) );
	}
	
	/**
	 * The sort method sorts the collection against the supplied callback.
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function sort( \Closure $f )
	{
		usort( $this->_data, $f );
		
		return $this;
	}
	
	/**
	 * The splice method removes a portion of the collection and replaces it with something else.
	 *
	 * @param int        $spliceIndex
	 * @param null|int   $length
	 * @param null|array $replacement
	 *
	 * @return CollectionInterface
	 */
	public function splice( $spliceIndex, $length = NULL, $replacement = NULL )
	{
		if ( ! is_null( $length ) ) settype( $length, 'integer' );
		
		settype( $spliceIndex, 'integer' );
		
		$inputArray = $this->all();
		
		array_splice( $inputArray, $spliceIndex, $length, $replacement );
		
		return static::instance( $inputArray );
	}
	
	/**
	 * The toArray method converts the collection into a plain PHP array.
	 * If the collection's values are Model objects,
	 * the models will also be converted to arrays
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->map( function ( $item, $index )
		{
			if ( $item instanceof ModelInterface ) return $item->toArray();
			
			elseif ( $item instanceof \JsonSerializable ) return $item->jsonSerialize();
			
			elseif ( $item instanceof CollectionInterface ) return $item->toArray();
			
			else return $item;
			
		} )->all();
	}
	
	/**
	 * The toJson method converts the collection into JSON
	 *
	 * @param int $options
	 * @param int $depth
	 *
	 * @return string
	 */
	public function toJson( $options = 0, $depth = 512 )
	{
		return json_encode( $this->jsonSerialize(), $options, $depth );
	}
	
	/**
	 * The where method returns a new collection of items that pass a given truth test
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function where( \Closure $f )
	{
		$newCollection = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( $f( $item, $index ) ) $newCollection->set( $index, $item );
		}
		
		return $newCollection;
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
		return $this->toArray();
	}
	
	/**
	 * The findWhere method returns the first item that passes a given truth test, or NULL
	 *
	 * @param \Closure $f
	 *
	 * @return mixed|null
	 */
	public function findWhere( \Closure $f )
	{
		foreach ( $this->all() as $index => $item )
		{
			if ( $f( $item, $index ) ) return $item;
		}
		
		return NULL;
	}
	
	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset )
	{
		return array_key_exists( $offset, $this->_data );
	}
	
	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset )
	{
		return $this->get( $offset );
	}
	
	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value )
	{
		$this->set( $offset, $value );
	}
	
	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset )
	{
		$this->remove( $offset );
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
		return new \ArrayIterator( $this->all() );
	}
	
	/**
	 * The add method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function add( $item )
	{
		return $this->push( $item );
	}
	
	/**
	 * @param $separator
	 *
	 * @return string
	 */
	public function join( $separator )
	{
		return implode( $separator, $this->toArray() );
	}
}