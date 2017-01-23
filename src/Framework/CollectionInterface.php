<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 8:30 PM
 */

namespace Framework;

interface CollectionInterface {
	
	/**
	 * The add method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function add( $item );
	
	/**
	 * The all method returns the underlying array represented by the collection
	 *
	 * @return array
	 */
	public function all();
	
	/**
	 * The append method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function append( $item );
	
	/**
	 * The at method returns the item at a given index in the collection
	 *
	 * @param int $index
	 *
	 * @return array
	 */
	public function at( $index );
	
	/**
	 * The chunk method breaks the collection into multiple, smaller collections of a given size
	 *
	 * @param $collectionSize
	 *
	 * @return CollectionInterface
	 */
	public function chunk( $collectionSize );
	
	/**
	 * The collapse method collapses a collection of arrays into a single, flat collection
	 *
	 * @return CollectionInterface
	 */
	public function collapse();
	
	/**
	 * The count method returns the total number of items in the collection
	 *
	 * @return int
	 */
	public function count();
	
	/**
	 * The each method iterates over the items in the collection and passes each item to a callback.
	 * Return FALSE to exit the loop.
	 *
	 * @param \Closure $f
	 * @param bool     $passByReference
	 *
	 * @return CollectionInterface
	 */
	public function each( \Closure $f, $passByReference = FALSE );
	
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
	public function filter( \Closure $f );
	
	/**
	 * Returns the first item in the underlying array.
	 *
	 * @return mixed
	 */
	public function first();
	
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
	public function forPage( $pageNumber, $itemsPerPage );
	
	/**
	 * Gets a value from the collection by a given key.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get( $key );
	
	/**
	 * The includes method passes each item in the collection to a callback
	 * and returns true at the first item that returns true from the callback, or false
	 *
	 * @param \Closure $f
	 *
	 * @return bool
	 */
	public function includes( \Closure $f );
	
	/**
	 * The isEmpty method returns true if the collection is empty; otherwise, false is returned
	 *
	 * @return bool
	 */
	public function isEmpty();
	
	/**
	 * @param $separator
	 *
	 * @return string
	 */
	public function join( $separator );
	
	/**
	 * Returns the first item in the underlying array.
	 * @return mixed
	 */
	public function last();
	
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
	public function map( \Closure $f );
	
	/**
	 * The pop method removes and returns the last item from the collection
	 *
	 * @return mixed
	 */
	public function pop();
	
	/**
	 * The prepend method adds an item to the beginning of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function prepend( $item );
	
	/**
	 * The push method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return CollectionInterface
	 */
	public function push( $item );
	
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
	public function reduce( \Closure $f, $carry = NULL );
	
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
	public function reject( \Closure $f );
	
	/**
	 * Removes an item from the collection at a given key
	 *
	 * @param $key
	 *
	 * @return CollectionInterface
	 */
	public function remove( $key );
	
	/**
	 * The reverse method reverses the order of the collection's items
	 *
	 * @return CollectionInterface
	 */
	public function reverse();
	
	/**
	 * The set method sets a value by a given key on the collection
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return CollectionInterface
	 */
	public function set( $key, $value );
	
	/**
	 * The shift method removes and returns the first item from the collection
	 *
	 * @return mixed
	 */
	public function shift();
	
	/**
	 * The slice method returns a slice of the collection starting at the given index
	 *
	 * @param int $startIndex
	 * @param     $length
	 *
	 * @return CollectionInterface
	 */
	public function slice( $startIndex = 0, $length );
	
	/**
	 * The sort method sorts the collection against the supplied callback.
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function sort( \Closure $f );
	
	/**
	 * The splice method removes a portion of the collection and replaces it with something else.
	 *
	 * @param int        $spliceIndex
	 * @param int|null   $length
	 * @param array|null $replacement
	 *
	 * @return CollectionInterface
	 */
	public function splice( $spliceIndex, $length = NULL, $replacement = NULL );
	
	/**
	 * The toArray method converts the collection into a plain PHP array.
	 * If the collection's values are Model objects,
	 * the models will also be converted to arrays
	 *
	 * @return array
	 */
	public function toArray();
	
	/**
	 * The toJson method converts the collection into JSON
	 *
	 * @param int $options
	 * @param int $depth
	 *
	 * @return string
	 */
	public function toJson( $options = 0, $depth = 512 );
	
	/**
	 * The where method returns a new collection of items that pass a given truth test
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function where( \Closure $f );
	
	/**
	 * The findWhere method returns the first item that passes a given truth test, or NULL
	 *
	 * @param \Closure $f
	 *
	 * @return mixed
	 */
	public function findWhere( \Closure $f );
	
}