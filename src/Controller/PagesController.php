<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Cache\Cache;
use Anticus\View\View;
use Wruczek\PhpFileCache\PhpFileCache;

/**
 * Pages controller
 */
class PagesController extends AppController
{
    /**
     * An instance of App\Cache\Cache
     *
     * @var Cache
     */
    private $cache;

    /**
     * Path to cache folder
     *
     * @var string
     */
    private $cachePath;
    
    /**
     * Class constructor
     *
     * @param array<string,string> $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        $this->cache = new Cache();
        $this->cachePath = $this->data['config']['paths']['Cache'];
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

        $this->data['page'] = $cache->cacheData(
            $this->cachePath . DS . 'page_view' . DS, 
            'view' . $cache_name,
            'App\Model\Pages', 
            'getByUrl', 
            $this->data['here']
        );

        if ($this->data['here'] == '/') {
            $blog_posts = $cache->cacheData(
                $this->cachePath . DS . 'blog_latest' . DS, 
                'latest',
                'App\Model\BlogPosts', 
                'getLatest');
            $this->data['blog_posts'] = $blog_posts;
            $view = 'home.html';
        } else {
            $view = 'view.html';
        }

        $children = [];
        $siblings = [];
        $key = 0;
        foreach ($this->data['all_pages'] as $page) {
            $page_parts = explode('/', $page['url']);
            
            if ( isset($page_parts[2]) && $this->data['here'] == DS . $page_parts[1] ) {
                $this->data['children'][$key]['url'] = $page['url'];
                $this->data['children'][$key]['menu_title'] = $page['menu_title'];
                $key++;
            }

            $here_parts = explode('/', $this->data['here']);

            if ( isset($here_parts[2]) && isset($page_parts[2]) && $here_parts[1] == $page_parts[1] ) {
                $this->data['siblings'][$key]['url'] = $page['url'];
                $this->data['siblings'][$key]['menu_title'] = $page['menu_title'];
                $key++;
            }
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
        $this->cache->clearCache([$this->cachePath . DS . 'pages' . DS]);
    }

    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearViewCacheAction()
    {
        $this->cache->clearCache([$this->cachePath . DS . 'page_view' . DS]);
    }

    /**
     * Clear all blog caches.
     *
     * @return void
     */
    public function clearAllCacheAction()
    {
        $paths = [
            $this->cachePath . DS . 'pages' . DS,
            $this->cachePath . DS . 'page_view' . DS
        ];
        $this->cache->clearCache($paths);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
