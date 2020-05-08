<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define("ROOT", dirname(__DIR__, 1));
define("CONFIG", ROOT . DS . 'config');
define("WEBROOT", ROOT . DS . 'public');
define("APP", ROOT . DS . 'src');

require ROOT. DS . 'vendor' . DS . 'autoload.php';

set_error_handler('Paulus\Error\Error::errorHandler');
set_exception_handler('Paulus\Error\Error::exceptionHandler');

$router = new App\Router\PageRouter();

// Add the routes
$router->add('blog', [
    'controller' => 'BlogPostsController',
    'action' => 'index'
]);
$router->add('blog/clear-index-cache', [
    'controller' => 'BlogPostsController',
    'action' => 'clearIndexCache'
]);
$router->add('blog/clear-view-cache', [
    'controller' => 'BlogPostsController',
    'action' => 'clearViewCache'
]);
$router->add('blog/{slug}', [
    'controller' => 'BlogPostsController',
    'action' => 'view'
]);
$router->add('feed', [
    'controller' => 'BlogPostsController',
    'action' => 'feed'
]);

// clear caches — can be used by webhooks in Directus
$router->add('pages/clear-index-cache', [
    'controller' => 'PagesController',
    'action' => 'clearIndexCache'
]);
$router->add('pages/clear-view-cache', [
    'controller' => 'PagesController',
    'action' => 'clearViewCache'
]);
$router->add('main-menu/clear-cache', [
    'controller' => 'MainMenuController',
    'action' => 'clearCache'
]);

// remove trailing forward slashes and remove query string
$url = strtok(trim($_SERVER['REQUEST_URI'], '/'), '?');

// to avoid regenerating the cache
if ($url != 'pages/clear-index-cache') {
    // add routes for all published pages in the db.
    $router->addPages();
}
// To debug the routes:
// debug($router->getRoutes());

$router->dispatch($url, '/');
