<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 7:15 PM
 */

namespace Framework;

use Database\MySQL;
use Twig_Environment;
use Twig_Extensions_Extension_Text;
use Twig_Loader_Filesystem;

class Container implements ContainerInterface {
	
	protected static $mySQL;
	
	/**
	 * @param array $options
	 *
	 * @return MySQL
	 */
	public static function db( $options = [] )
	{
		if ( self::$mySQL instanceof MySQL ) return self::$mySQL;
		
		$defaults = [
			'host'     => getenv( 'DB_HOST' ),
			'database' => getenv( 'DB_DATABASE' ),
			'username' => getenv( 'DB_USERNAME' ),
			'password' => getenv( 'DB_PASSWORD' ),
		];
		
		self::$mySQL = new MySQL( NULL, array_merge( $defaults, $options ) );
		
		return self::$mySQL;
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