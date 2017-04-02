<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 7:15 PM
 */

namespace Framework;

use Exception;
use PDO;
use Slim\PDO\Database;
use Twig_Environment;
use Twig_Extensions_Extension_Text;
use Twig_Loader_Filesystem;

/**
 * Class Container
 * @package Framework
 */
class Container implements ContainerInterface {
	
	const DSN_FORMAT = 'mysql:host=%s;dbname=%s;port=%s;charset=%s';
	/**
	 * @var array
	 */
	protected static $pdoOpts = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => FALSE,
		PDO::ATTR_STRINGIFY_FETCHES  => FALSE,
	];
	/**
	 * @var Database
	 */
	protected static $dbInstance;
	
	public static $connectionParams = [];
	
	protected static $exceptionHandler = NULL;
	
	/**
	 * @return Database
	 */
	public static function getDbInstance()
	{
		return self::$dbInstance;
	}
	
	/**
	 * @param Database $dbInstance
	 */
	public static function setDbInstance( Database $dbInstance )
	{
		self::$dbInstance = $dbInstance;
	}
	
	public static function compileDSN( array $con )
	{
		return sprintf(
			self::DSN_FORMAT,
			$con['host'], $con['database'], $con['port'], $con['charset']
		);
	}
	
	/**
	 * @param array $options
	 * @param bool  $forceNewConnection
	 * @param bool  $setOnContainer
	 *
	 * @return null|Database
	 * @throws Exception
	 */
	public static function db( $options = [], $forceNewConnection = FALSE, $setOnContainer = TRUE )
	{
		$hasInstance = self::$dbInstance instanceof Database;
		// return cached connection
		if ( $hasInstance && ! $forceNewConnection ) return self::getDbInstance();
		// force a new connection and close old connection
		if ( $hasInstance && $forceNewConnection && $setOnContainer ) self::$dbInstance = NULL;
		
		$envParams = [
			'host'     => getenv( 'DB_HOST' ),
			'database' => getenv( 'DB_DATABASE' ),
			'port'     => getenv( 'DB_PORT' ),
			'charset'  => getenv( 'DB_CHARSET' ),
			'usr'      => getenv( 'DB_USERNAME' ),
			'pwd'      => getenv( 'DB_PASSWORD' ),
		];
		
		$params = array_merge( $envParams, $options );
		if ( $setOnContainer ) self::$connectionParams = $params;
		
		try
		{
			$instance = new Database(
				self::compileDSN( $params ), $params['usr'], $params['pwd'], self::$pdoOpts
			);
			if ( $setOnContainer ) self::setDbInstance( $instance );
			
			return $instance;
		}
		catch ( Exception $e )
		{
			if ( ! is_null( self::$exceptionHandler ) ) call_user_func( self::$exceptionHandler, $e );
			else throw $e;
			
			return NULL;
		}
	}
	
	/**
	 * @param  $fn
	 */
	public static function setExceptionHandler( $fn )
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