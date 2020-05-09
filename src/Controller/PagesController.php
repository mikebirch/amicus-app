<?php

namespace App\Controller;

use App\Controller\AppController;
use Amicus\Cache\Cache;
use App\Model\Pages;
use App\Model\BlogPosts;
use Amicus\View\View;

/**
 * Pages controller
 */
class PagesController extends AppController
{
    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        $this->cache = new Cache();
    }
    
    /**
     * Show the index page
     *
     * @return void
     */
    public function viewAction()
    {
        $cache = new Cache();

        $cache_name = $this->data['here'];
        if ($cache_name == '/') {
            $cache_name = '-home';
        }
        $cache_name = str_replace('/', '-', $cache_name);

        $cache_path = 'page-view' . DS  .  'view' . $cache_name . DS;
        $Pages = new Pages;
        $page = $cache->cacheData(
            'page-view' . DS, 
            'view' . $cache_name,
            $Pages, 
            'getByUrl', 
            $this->data['here']
        );

        $this->data['page'] = $page['page'];

        if ($this->data['here'] == '/') {
            $blogPosts = new BlogPosts;
            $blog_posts = $cache->cacheData(
                'blog-latest' . DS, 
                'latest',
                $blogPosts, 
                'getLatest');
            $this->data['blog_posts'] = $blog_posts['blog_posts'];
            $view = 'home.html';
        } else {
            $view = 'view.html';
        }

        View::renderTemplate('Pages' . DS . $view, $this->data);
    }

    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearIndexCacheAction()
    {
        $this->cache->clearCache(['pages' . DS]);
    }

    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearViewCacheAction()
    {
        $this->cache->clearCache(['page-view' . DS]);
    }
}
