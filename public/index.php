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

use Showus\Configure\Configure;
$config = Configure::read();

if ($config['environment'] == 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 
}
set_error_handler('Showus\Error\Error::errorHandler');
set_exception_handler('Showus\Error\Error::exceptionHandler');

require CONFIG . DS . 'requirements.php';

$router = new App\Router\PageRouter();

require CONFIG . DS . 'routes.php';

$router->dispatch($url, '/');
