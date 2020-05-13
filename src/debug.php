<?php
use Showus\Configure\Configure;
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define("ROOT", dirname(__DIR__, 1));
define("CONFIG", ROOT . DS . 'config');
define("APP", ROOT . DS . 'src');


if ( !function_exists('debug') ) {
    /**
     * debug variables
     *
     * @param mixed $var
     * @return void
     */
    function debug($var)
    {
        $config = Configure::read();
        if ( $config['environment'] != 'prod' && $config['debug'] == true ) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            echo '<strong>debug() called  in ' . $caller['file']. ' at line ' . $caller['line'] . '</strong>';
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        } 
    }
}
