<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 9:43 PM
 */

namespace Framework;

class Collection implements CollectionInterface {
	
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
		
		if ( $newCollection->isEmpty() ) $newCollection->push( $chunk );
		
		return $newCollection;
	}
	
	/**
	 * The collapse method collapses a collection of arrays into a single, flat collection
	 *
	 * @return CollectionInterface
	 */
	public function collapse()
	{
		// TODO: Implement collapse() method.
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
			if ( $f( $item, $index ) ) $filtered->push( $item );
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
		// TODO: Implement forPage() method.
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
		// TODO: Implement includes() method.
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
			$mapped->push( $f( $item, $index ) );
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
		$filtered = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( ! $f( $item, $index ) ) $filtered->push( $item );
		}
		
		return $filtered;
	}
	
	/**
	 * The reverse method reverses the order of the collection's items
	 *
	 * @return CollectionInterface
	 */
	public function reverse()
	{
		array_reverse( $this->_data );
		
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
	public function slice( $startIndex = 0, $length )
	{
		// TODO: Implement slice() method.
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
		// TODO: Implement sort() method.
	}
	
	/**
	 * The splice method removes and returns a slice of items starting at the specified index
	 *
	 * @param int $spliceIndex
	 *
	 * @return CollectionInterface
	 */
	public function splice( $spliceIndex )
	{
		// TODO: Implement splice() method.
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
		// TODO: Implement toArray() method.
	}
	
	/**
	 * The toJson method converts the collection into JSON
	 *
	 * @return string
	 */
	public function toJson()
	{
		// TODO: Implement toJson() method.
	}
	
	/**
	 * The where method returns the where element in the collection that passes a given truth test
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function where( \Closure $f )
	{
		// TODO: Implement where() method.
	}
}