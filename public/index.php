<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define("ROOT", dirname(__DIR__, 1));
define("CONFIG", ROOT . DS . 'config');
define("CACHE", ROOT . DS . 'cache');
define("LOGS", ROOT . DS . 'logs');
define("WEBROOT", ROOT . DS . 'public');
define("APP", ROOT . DS . 'src');

require ROOT. DS . 'vendor' . DS . 'autoload.php';

use Anticus\Configure\Configure;
$config = Configure::read();

if ($config['environment'] == 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 
}
set_error_handler('Anticus\Error\Error::errorHandler');
set_exception_handler('Anticus\Error\Error::exceptionHandler');

require CONFIG . DS . 'requirements.php';

$router = new App\Router\PageRouter();

require CONFIG . DS . 'routes.php';

$router->dispatch($url, '/');


if ($config['show_debug_bar'] == true) {
    $db_config = $config['Datasources'][$config['environment']];
    
    $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    $debug =  '<div class="debug-bar">';
    $debug .= '<div>';
    $debug .= '<div><p>Execution time: ' . number_format($time, 3) . ' milliseconds</p></div>';
    $debug .= '<div>';
    $debug .= '<h2>Clear Caches</h2>';
    $debug .= '<ul>';
    $debug .= '<li><a href="/blog/clear-all-cache">Blog</a></li>';
    $debug .= '<li><a href="/pages/clear-all-cache">Pages</a></li>';
    $debug .= '</ul>';
    $debug .= '</div>';
    $debug .= '</div>';
    $debug .= '</div>';
    echo $debug;
}
