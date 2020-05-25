<?php

namespace App\Controller;

use Anticus\Controller\Controller;
use Anticus\Configure\Configure;
use App\Cache\Cache;

/**
 * Pages controller
 */
class AppController extends Controller
{

    /**
     * data to be passed to views
     *
     * @var array<mixed>
     */
    public $data;
    
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
      
        $this->data['config'] = Configure::read();
        // to prevent sensitive data being displayed in a view
        unset($this->data['config']['Datasources']); 

        if ( !file_exists($this->data['config']['paths']['Template'] . DS . 'Pages' . DS . 'install.html') ) {
            $cache = new Cache();
            $this->data['menu_items'] = $cache->cacheData(
                $this->data['config']['paths']['Cache'] . DS . 'menu_items' . DS, 
                'all', 
                'App\Model\MenuItems', 
                'getAll'
            );

            $this->data['all_pages'] = $cache->retrieve(
                $this->data['config']['paths']['Cache'] . DS . 'pages' . DS,
                'all'
            );
        }

        if ($_SERVER['REQUEST_URI'] != '/') {
            // remove trailing forward slash 
            $this->data['here'] = rtrim($_SERVER['REQUEST_URI'], '/'); 
        } else {
            $this->data['here'] = $_SERVER['REQUEST_URI'];
        }
        
        // if the query string includes page=1, remove it from $this->data['here']
        // so that the main menu can show the active page and to allow it to be used for breadcrumbs
        if (isset($_GET['page']) && $_GET['page'] == 1) {
            $this->data['here'] = strtok($_SERVER['REQUEST_URI'], '?'); // remove the query string
        }
        
        // breadcrumbs - only one level down
        $parts = explode('/', ltrim($this->data['here'], '/'));
        
        if ( isset($parts[1]) ) {
            foreach ($this->data['all_pages'] as $page) {
                if ($page['url'] == DS . $parts[0]) {
                    $this->data['breadcrumbs'][0]['url'] = $page['url'];
                    $this->data['breadcrumbs'][0]['menu_title'] = $page['menu_title'];
                }
                if ($page['url'] == $this->data['here']) {
                    $this->data['breadcrumbs'][1]['url'] = null;
                    $this->data['breadcrumbs'][1]['menu_title'] = $page['menu_title'];
                }
            }
        }

         // create query string for CSS file for development
         $css_file = $this->data['config']['paths']['Webroot'] . DS . 'css' . DS . 'screen-v' . $this->data['config']['site']['css_version'] . '.min.css';
         if (file_exists($css_file)) {
             $this->data['site']['css_mod_time'] = filemtime($css_file);
         } else {
             $this->data['site']['css_mod_time'] = '1';
         }
    }
}
