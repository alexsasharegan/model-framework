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
	 * The all method returns the underlying array represented by the collection
	 *
	 * @return array
	 */
	public function all();
	
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
	 * The each method iterates over the items in the collection and passes each item to a callback
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function each( \Closure $f );
	
	/**
	 * The every method creates a new collection consisting of every n-th element
	 *
	 * @param $nthElement
	 *
	 * @return CollectionInterface
	 */
	public function every( $nthElement );
	
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
	 * The first method returns the first element in the collection that passes a given truth test
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function first( \Closure $f );
	
	/**
	 * The flatMap method iterates through the collection and passes each value to the given callback.
	 * The callback is free to modify the item and return it,
	 * thus forming a new collection of modified items.
	 * Then, the array is flattened by a level
	 *
	 * @param \Closure $f
	 *
	 * @return CollectionInterface
	 */
	public function flatMap( \Closure $f );
	
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
	 * @return mixed
	 */
	public function prepend( $item );
	
	/**
	 * The push method appends an item to the end of the collection
	 *
	 * @param $item
	 *
	 * @return mixed
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
	 * @return mixed
	 */
	public function reject(\Closure $f);
	
	/**
	 * The reverse method reverses the order of the collection's items
	 *
	 * @return mixed
	 */
	public function reverse();
	
	public function shift();
	
	public function shuffle();
	
	public function slice();
	
	public function sort();
	
	public function sortBy();
	
	public function sortByDesc();
	
	public function splice();
	
	public function sum();
	
	public function take();
	
	public function toArray();
	
	public function toJson();
	
	public function transform();
	
	public function union();
	
	public function unique();
	
	public function values();
	
	public function where();
	
	public function whereStrict();
	
	public function whereIn();
	
	public function whereInLoose();
	
	public function zip();
}