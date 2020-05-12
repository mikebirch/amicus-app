<?php

namespace App\Router;

use Showus\Router\Router;
use Showus\Configure\Configure;
use App\Cache\Cache;

/**
 * PageRouter class
 * 
 * Adds routes for pages from the pages table in the database
 */
class PageRouter extends Router
{
    /**
     * Add routes from the pages table in the database. 
     * This is better than using a greedy route because the db query is cached 
     * and it avoids having to write routes in the format: pages/{slug}
     *
     * @return void
     */
    public function addPages()
    {
        $cache = new Cache();
        $config = Configure::read();
        $published_pages = $cache->cacheData(
            $config['paths']['Cache'] . DS . 'pages' . DS, 
            'all', 
            'App\Model\Pages', 
            'getAll'
        );
        foreach ($published_pages as $published_page) {
            $this->add(trim($published_page['url'], '/'), [
                'controller' => 'PagesController',
                'action' => 'view'
            ]);
        }
    }
}
