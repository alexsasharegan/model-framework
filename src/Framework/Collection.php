<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 9:43 PM
 */

namespace Framework;

class Collection implements CollectionInterface {
	
	/**
	 * The all method returns the underlying array represented by the collection
	 *
	 * @return array
	 */
	public function all()
	{
		// TODO: Implement all() method.
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
		// TODO: Implement append() method.
	}
	
	/**
	 * The chunk method breaks the collection into multiple, smaller collections of a given size
	 *
	 * @param $collectionSize
	 *
	 * @return CollectionInterface
	 */
	public function chunk( $collectionSize )
	{
		// TODO: Implement chunk() method.
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
		// TODO: Implement count() method.
	}
	
	/**
	 * The each method iterates over the items in the collection and passes each item to a callback
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function each( \Closure $f )
	{
		// TODO: Implement each() method.
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
		// TODO: Implement filter() method.
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
		// TODO: Implement isEmpty() method.
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
		// TODO: Implement map() method.
	}
	
	/**
	 * The pop method removes and returns the last item from the collection
	 *
	 * @return mixed
	 */
	public function pop()
	{
		// TODO: Implement pop() method.
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
		// TODO: Implement prepend() method.
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
		// TODO: Implement push() method.
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
		// TODO: Implement reduce() method.
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
		// TODO: Implement reject() method.
	}
	
	/**
	 * The reverse method reverses the order of the collection's items
	 *
	 * @return CollectionInterface
	 */
	public function reverse()
	{
		// TODO: Implement reverse() method.
	}
	
	/**
	 * The shift method removes and returns the first item from the collection
	 *
	 * @return mixed
	 */
	public function shift()
	{
		// TODO: Implement shift() method.
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