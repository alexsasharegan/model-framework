<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 7:15 PM
 */

namespace Framework;

use Database\MySQL;
use Exception;
use Twig_Environment;
use Twig_Extensions_Extension_Text;
use Twig_Loader_Filesystem;

/**
 * Class Container
 * @package Framework
 */
class Container implements ContainerInterface {
	
	/**
	 * @var MySQL
	 */
	protected static $mySQL;
	
	/**
	 * @var callable
	 */
	protected static $exceptionHandler;
	
	/**
	 * @param array $options
	 * @param bool  $forceNewConnection
	 *
	 * @return MySQL
	 * @throws Exception
	 */
	public static function db( $options = [], $forceNewConnection = FALSE )
	{
		// return cached connection
		if ( self::$mySQL instanceof MySQL && ! $forceNewConnection ) return self::$mySQL;
		// force a new connection and close old connection via destructor
		if ( self::$mySQL instanceof MySQL && $forceNewConnection ) self::$mySQL = NULL;
		
		$defaults = [
			'host'     => getenv( 'DB_HOST' ),
			'database' => getenv( 'DB_DATABASE' ),
			'username' => getenv( 'DB_USERNAME' ),
			'password' => getenv( 'DB_PASSWORD' ),
		];
		
		try
		{
			self::$mySQL = new MySQL( NULL, array_merge( $defaults, $options ) );
		}
		catch ( Exception $e )
		{
			is_callable( self::$exceptionHandler )
				? call_user_func( self::$exceptionHandler, $e )
				: self::reThrowException( $e );
		}
		
		return self::$mySQL;
	}
	
	/**
	 * @param Exception $exception
	 *
	 * @throws Exception
	 */
	protected static function reThrowException( Exception $exception )
	{
		throw $exception;
	}
	
	/**
	 * @param callable $fn
	 */
	public static function setExceptionHandler( callable $fn )
	{
		self::$exceptionHandler = $fn;
	}
	
	/**
	 * @param string $templateLocation
	 *
	 * @return Twig_Environment
	 */
	public static function getTwigInstance( $templateLocation = __DIR__ )
	{
		$loader = new Twig_Loader_Filesystem( $templateLocation );
		$twig   = new Twig_Environment( $loader );
		$twig->addExtension( new Twig_Extensions_Extension_Text() );
		
		return $twig;
	}
	
}