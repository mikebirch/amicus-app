<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Cache\Cache;
use Anticus\View\View;

/**
 * BlogPosts controller
 */
class BlogPostsController extends AppController
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
    public function indexAction()
    {
        if (isset($_GET['page'])) {
            $current_page = intval($_GET['page']);
        } else {
            $current_page = 1;
        }

        $tag = null;
        if ( isset($this->route_params['tag']) ) {
            $tag = $this->route_params['tag'];
            $file = $tag;
        } else {
            $file = 'index';
        }
        $blog_posts = $this->cache->cacheData(
            $this->cachePath . DS . 'blog_index' . DS, 
            $file . '-' . $current_page,
            'App\Model\BlogPosts', 
            'getPage', 
            ['current_page' => $current_page, 'tag' => $tag]
        );
        $this->data = $this->data + $blog_posts;
        View::renderTemplate('BlogPosts' . DS . 'index.html', $this->data);
    }

    /**
     * Show the view page
     *
     * @return void
     */
    public function viewAction()
    {
        $blog_post = $this->cache->cacheData(
            $this->cachePath . DS . 'blog-view' . DS, 
            'view-' . $this->route_params['slug'],
            'App\Model\BlogPosts', 
            'getBySlug', 
            $this->route_params['slug']
        );
        $this->data['blog_post'] = $blog_post;
        if (!empty($this->data['blog_post'])) {
            View::renderTemplate('BlogPosts' . DS . 'view.html', $this->data);
        } else {
            throw new \Exception('Page not found: /blog/' . $this->route_params['slug'], 404);
        }     
    }
    
    /**
     * RSS feed
     *
     * @return void
     */
    public function feedAction()
    {
        $blog_posts = $this->cache->cacheData(
            $this->cachePath . DS . 'blog-feed' . DS, 
            'feed',
            'App\Model\BlogPosts', 
            'getAll'
        );
        $this->data['blog_posts'] = $blog_posts;
        View::render('BlogPosts' . DS . 'rss.php', $this->data);
    }

    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearIndexCacheAction()
    {
        $paths = [
            $this->cachePath . DS . 'blog-index' . DS,
            $this->cachePath . DS . 'blog-latest' . DS,
            $this->cachePath . DS . 'blog-feed' . DS
        ];
        $this->cache->clearCache($paths);
    }

    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearViewCacheAction()
    {
        $paths = [
            $this->cachePath . DS . 'blog-view' . DS,
        ];
        $this->cache->clearCache($paths);
    }

    /**
     * Clear all blog caches
     *
     * @return void
     */
    public function clearAllCacheAction()
    {
        $paths = [
            $this->cachePath . DS . 'blog-index' . DS,
            $this->cachePath . DS . 'blog-latest' . DS,
            $this->cachePath . DS . 'blog-feed' . DS,
            $this->cachePath . DS . 'blog-view' . DS
        ];
        $this->cache->clearCache($paths);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
