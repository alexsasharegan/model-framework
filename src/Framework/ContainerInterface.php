<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/27/16
 * Time: 7:40 PM
 */

namespace Framework;

use Database\MySQL;
use Twig_Environment;

/**
 * Interface ContainerInterface
 * @package Framework
 */
interface ContainerInterface {
    
    /**
     * @param array $options
     *
     * @return MySQL
     */
    public static function db( $options = [] );
    
    /**
     * @param string $templateLocation
     *
     * @return Twig_Environment
     */
    public static function getTwigInstance( $templateLocation = __DIR__ );
}
