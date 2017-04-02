<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 9:43 PM
 */

namespace Framework;

use Traversable;

/**
 * Class Collection
 * @package Framework
 */
class Collection implements CollectionInterface, \JsonSerializable, \ArrayAccess, \IteratorAggregate, \Countable {
	
	/**
	 * @var array
	 */
	private $data = [];
	
	/**
	 * Collection constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data = [] )
	{
		$this->data = $data;
	}
	
	/**
	 * @inheritdoc
	 */
	public function append( $item )
	{
		array_push( $this->data, $item );
		
		return $this;
	}
	
	/**
	 * @inheritdoc
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
	 * @param array $data
	 *
	 * @return static
	 */
	public static function instance( array $data = [] )
	{
		return new static( $data );
	}
	
	/**
	 * @inheritdoc
	 */
	public function count()
	{
		return count( $this->data );
	}
	
	/**
	 * @inheritdoc
	 */
	public function push( $item )
	{
		array_push( $this->data, $item );
		
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public function at( $index )
	{
		settype( $index, 'integer' );
		
		return isset( $this->data[ $index ] ) ? $this->data[ $index ] : NULL;
	}
	
	/**
	 * @inheritdoc
	 */
	public function isEmpty()
	{
		return empty( $this->data );
	}
	
	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function all()
	{
		return $this->data;
	}
	
	/**
	 * @inheritdoc
	 */
	public function each( $fn, $passByReference = FALSE )
	{
		$this->isCallable( $fn );
		
		if ( $passByReference )
		{
			foreach ( $this->all() as $index => &$item )
			{
				if ( call_user_func( $fn, $item, $index ) === FALSE ) break;
			}
		}
		else
		{
			foreach ( $this->all() as $index => $item )
			{
				if ( call_user_func( $fn, $item, $index ) === FALSE ) break;
			}
		}
		
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public function filter( $fn )
	{
		$this->isCallable( $fn );
		$filtered = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( call_user_func( $fn, $item, $index ) ) $filtered->set( $index, $item );
		}
		
		return $filtered;
	}
	
	/**
	 * @inheritdoc
	 */
	public function set( $key, $value )
	{
		$this->data[ $key ] = $value;
		
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public function first()
	{
		$data = $this->all();
		
		return reset( $data );
	}
	
	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function slice( $startIndex = 0, $length = NULL )
	{
		if ( ! is_null( $length ) ) settype( $length, 'integer' );
		
		settype( $startIndex, 'integer' );
		
		return static::instance( array_slice( $this->all(), $startIndex, $length ) );
	}
	
	/**
	 * @inheritdoc
	 */
	public function includes( $fn )
	{
		$this->isCallable( $fn );
		
		foreach ( $this->all() as $index => $item )
		{
			if ( call_user_func( $fn, $item, $index ) ) return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * @inheritdoc
	 */
	public function last()
	{
		$data = $this->all();
		
		return end( $data );
	}
	
	/**
	 * @inheritdoc
	 */
	public function pop()
	{
		return array_pop( $this->data );
	}
	
	/**
	 * @inheritdoc
	 */
	public function prepend( $item )
	{
		array_unshift( $this->data, $item );
		
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public function reduce( $fn, $carry = NULL )
	{
		$this->isCallable( $fn );
		
		foreach ( $this->all() as $index => $item )
		{
			$carry = call_user_func( $fn, $carry, $item, $index );
		}
		
		return $carry;
	}
	
	/**
	 * @inheritdoc
	 */
	public function reject( $fn )
	{
		$this->isCallable( $fn );
		$rejected = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( ! call_user_func( $fn, $item, $index ) ) $rejected->set( $index, $item );
		}
		
		return $rejected;
	}
	
	/**
	 * @inheritdoc
	 */
	public function reverse()
	{
		return static::instance( array_reverse( $this->all() ) );
	}
	
	/**
	 * @inheritdoc
	 */
	public function shift()
	{
		return array_shift( $this->data );
	}
	
	/**
	 * @inheritdoc
	 */
	public function sort( $f )
	{
		$this->isCallable( $f );
		usort( $this->data, $f );
		
		return $this;
	}
	
	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function toJson( $options = 0, $depth = 512 )
	{
		return json_encode( $this->jsonSerialize(), $options, $depth );
	}
	
	/**
	 * @inheritdoc
	 */
	function jsonSerialize()
	{
		return $this->toArray();
	}
	
	/**
	 * @inheritdoc
	 */
	public function toArray()
	{
		return $this->map( function ( $item, $index )
		{
			if ( $item instanceof ModelInterface ) return $item->toArray();
			
			elseif ( $item instanceof CollectionInterface ) return $item->toArray();
			
			elseif ( $item instanceof \JsonSerializable ) return $item->jsonSerialize();
			
			else return $item;
			
		} )->all();
	}
	
	/**
	 * @inheritdoc
	 */
	public function map( $fn, $returnAsArray = FALSE )
	{
		$this->isCallable( $fn );
		$mapped = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			$mapped->set( $index, call_user_func( $fn, $item, $index ) );
		}
		
		return $returnAsArray ? $mapped->toArray() : $mapped;
	}
	
	/**
	 * @inheritdoc
	 */
	public function where( $f )
	{
		$this->isCallable( $f );
		$newCollection = static::instance();
		
		foreach ( $this->all() as $index => $item )
		{
			if ( call_user_func( $f, $item, $index ) ) $newCollection->set( $index, $item );
		}
		
		return $newCollection;
	}
	
	/**
	 * @inheritdoc
	 */
	public function findWhere( $f )
	{
		$this->isCallable( $f );
		
		foreach ( $this->all() as $index => $item )
		{
			if ( call_user_func( $f, $item, $index ) ) return $item;
		}
		
		return NULL;
	}
	
	/**
	 * @inheritdoc
	 */
	public function offsetExists( $offset )
	{
		return array_key_exists( $offset, $this->data );
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
	 * @inheritdoc
	 */
	public function get( $key )
	{
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : NULL;
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
	 * @inheritdoc
	 */
	public function remove( $key )
	{
		unset( $this->data[ $key ] );
		
		return $this;
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
	 * @inheritdoc
	 */
	public function add( $item )
	{
		return $this->push( $item );
	}
	
	/**
	 * @inheritdoc
	 */
	public function join( $separator )
	{
		return $this->implode( $separator );
	}
	
	/**
	 * @inheritdoc
	 */
	public function implode( $separator )
	{
		return implode( $separator, $this->toArray() );
	}
	
	/**
	 * @param      $fn
	 * @param bool $shouldThrow
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function isCallable( $fn, $shouldThrow = TRUE )
	{
		if ( ! is_callable( $fn ) && $shouldThrow )
		{
			throw new \InvalidArgumentException(
				sprintf( "Expected a callable type, but received %s instead.", gettype( $fn ) )
			);
		}
		
		return TRUE;
	}
	
	/**
	 * @param \PDOStatement $stmt
	 * @param               $fn
	 * @param null          $fetchStyle
	 *
	 * @return static
	 */
	public static function mapPDOStatement( \PDOStatement $stmt, $fn, $fetchStyle = NULL )
	{
		$mapped = new static();
		
		if ( is_null( $fn ) ) $fn = 'identity';
		
		while ( $row = $stmt->fetch( $fetchStyle ) ) $mapped->push( call_user_func( $fn, $row ) );
		
		return $mapped;
	}
	
	/**
	 * @param \PDOStatement $stmt
	 * @param               $fn
	 * @param null          $initial
	 * @param null          $fetchStyle
	 *
	 * @return mixed|null
	 */
	public static function reducePDOStatement( \PDOStatement $stmt, $fn, $initial = NULL, $fetchStyle = NULL )
	{
		$carry = $initial;
		
		while ( $row = $stmt->fetch( $fetchStyle ) ) $carry = call_user_func( $fn, $carry, $row );
		
		return $carry;
	}
}