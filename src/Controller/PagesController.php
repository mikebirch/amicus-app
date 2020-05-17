<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Cache\Cache;
use Anticus\View\View;

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
     * Path to logs folder
     *
     * @var string
     */
    private $logsPath;
    
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
        $this->logsPath = $this->data['config']['paths']['Logs'];
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
     * Show the install page
     *
     * @return void
     */
    public function installAction()
    {
        if (version_compare(PHP_VERSION, '7.0') < 0) {
            $this->data['requirements']['php_message'] = 'Current PHP version: ' . phpversion() . '.  It needs to be equal to or higher than 7.0 for Anticus.';
            $this->data['requirements']['php_fail'] = true;
        } else {
            $this->data['requirements']['php_message'] = 'Current PHP version: ' . phpversion();
            $this->data['requirements']['php_pass'] = true;
        }
        
        if (is_writable($this->cachePath)) {
            $this->data['requirements']['cache_message'] = 'Your cache directory is writable.' . PHP_EOL;
            $this->data['requirements']['cache_pass'] = true;
        } else {
            $this->data['requirements']['cache_message'] = 'Your cache directory, <code>'. $this->cachePath . '</code>, is not writable.' . PHP_EOL;
            $this->data['requirements']['cache_message']  .= 'The current permissions are: <code>' . substr(sprintf('%o', fileperms($this->cachePath)), -4) . '</code>';
            $this->data['requirements']['cache_fail'] = true;
        }
        
        if (is_writable($this->logsPath)) {
            $this->data['requirements']['logs_message'] = 'Your logs directory is writable.' . PHP_EOL;
            $this->data['requirements']['logs_pass'] = true;
        } else {
            $this->data['requirements']['logs_message'] = 'Your logs directory, <code>'. $this->logsPath . '</code>, is not writable.' . PHP_EOL;
            $this->data['requirements']['logs_message'] .= 'The current permissions are: <code>' . substr(sprintf('%o', fileperms($this->logsPath)), -4) . '</code>';
            $this->data['requirements']['logs_fail'] = true;
        }

        $pdo = \App\Model\Pages::testDatabaseConnection();
        if ( !empty($pdo) ){
            $this->data['requirements']['db_message'] = 'Anticus is able to connect to the database.' . PHP_EOL;
            $this->data['requirements']['db_pass'] = true;
        } else {
            $this->data['requirements']['db_message'] = 'Anticus is NOT able to connect to the database.' . PHP_EOL;
            $this->data['requirements']['db_fail'] = true;
        }
        View::renderTemplate('Pages' . DS . 'install.html', $this->data);
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
