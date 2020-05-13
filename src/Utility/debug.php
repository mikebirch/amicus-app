<?php
if (!function_exists('debug')) {
    /**
     * debug variables
     *
     * @param mixed $var
     * @return void
     */
    function debug($var)
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        echo '<strong>debug() called  in ' . $caller['file']. ' at line ' . $caller['line'] . '</strong>';
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}
