<?php

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
$router->add('blog/clear-all-cache', [
    'controller' => 'BlogPostsController',
    'action' => 'clearAllCache'
]);
$router->add('blog/{slug}', [
    'controller' => 'BlogPostsController',
    'action' => 'view'
]);
$router->add('feed', [
    'controller' => 'BlogPostsController',
    'action' => 'feed'
]);

/**
 * Add routes for clearing caches
 */
$router->add('pages/clear-index-cache', [
    'controller' => 'PagesController',
    'action' => 'clearIndexCache'
]);
$router->add('pages/clear-view-cache', [
    'controller' => 'PagesController',
    'action' => 'clearViewCache'
]);
$router->add('pages/clear-all-cache', [
    'controller' => 'PagesController',
    'action' => 'clearAllCache'
]);
$router->add('main-menu/clear-cache', [
    'controller' => 'MainMenuController',
    'action' => 'clearCache'
]);

/**
 * Add routes for pages
 */

// remove trailing forward slashes and remove query string
$url = strtok(trim($_SERVER['REQUEST_URI'], '/'), '?');

// to avoid regenerating the cache
if ($url != 'pages/clear-index-cache') {
    // add routes for all published pages in the db.
    $router->addPages();
}
