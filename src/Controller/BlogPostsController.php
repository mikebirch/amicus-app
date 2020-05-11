<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Cache\Cache;
use App\Model\BlogPosts;
use Amicus\View\View;

/**
 * BlogPosts controller
 */
class BlogPostsController extends AppController
{
    /**
     * An instance of App\Cache\Cache
     *
     * @var object
     */
    private $cache;

    /**
     * An instance of App\Model\BlogPosts
     *
     * @var object
     */
    private $blogPosts;
    
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
        $this->blogPosts = new BlogPosts;
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
       
        $blog_posts = $this->cache->cacheData(
            'blog-index' . DS, 
            'index-' . $current_page,
            $this->blogPosts, 
            'getPage', 
            $current_page
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
            'blog-view' . DS, 
            'view-' . $this->route_params['slug'],
            $this->blogPosts, 
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
            'blog-feed' . DS, 
            'feed',
            $this->blogPosts, 
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
            'blog-index' . DS,
            'blog-latest' . DS,
            'blog-feed' . DS
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
            'blog-view' . DS,
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
            'blog-index' . DS,
            'blog-latest' . DS,
            'blog-feed' . DS,
            'blog-view' . DS
        ];
        $this->cache->clearCache($paths);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
