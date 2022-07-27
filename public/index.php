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

$config = \Anticus\Configure\Configure::read();

error_reporting(E_ALL);

$whoops = new \Whoops\Run;
if ($config['environment'] == 'dev') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($exception, $inspector, $whoops) {
        \Anticus\Log\Log::logException($exception, $whoops);        
    });
}
$whoops->register();

require CONFIG . DS . 'requirements.php';

$router = new App\Router\PageRouter();

// remove trailing and forward slashes and remove query string
$url = strtok(trim($_SERVER['REQUEST_URI'], '/'), '?');

require CONFIG . DS . 'routes.php';

$router->dispatch($url, '/');

if ($config['show_debug_bar'] == true) {   
    $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    $debug =  '<div class="debug-bar">';
    $debug .= '<div>';
    $debug .= '<div><p>Execution time: ' . number_format($time, 3) . ' milliseconds</p></div>';
    $debug .= '<div>';
    $debug .= '<h2>Clear Caches</h2>';
    $debug .= '<ul>';
    $debug .= '<li><a href="/' . $config['blog']['url'] . '/clear-all-cache?path=' . $_SERVER['REQUEST_URI'] . '">Blog</a></li>';
    $debug .= '<li><a href="/pages/clear-all-cache?path=' . $_SERVER['REQUEST_URI'] . '">Pages</a></li>';
    $debug .= '<li><a href="/pages/clear-twig-cache?path=' . $_SERVER['REQUEST_URI'] . '">Twig</a></li>';
    $debug .= '</ul>';
    $debug .= '</div>';
    $debug .= '</div>';
    $debug .= '</div>';
    echo $debug;
}
