<?php

namespace App\Controller;

use Showus\Controller\Controller;
use App\Model\MainMenu;
use Showus\Configure\Configure;
use App\Cache\Cache;

/**
 * Pages controller
 */
class AppController extends Controller
{

    /**
     * data to be passed to views
     *
     * @var array
     */
    public $data;
    
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
      
        $this->data['config'] = Configure::read();
        // to prevent sensitive data being displayed in a view
        unset($this->data['config']['Datasources']); 

        $cache = new Cache();
        $MainMenu = new MainMenu;
        $this->data['main_menu_items'] = $cache->cacheData(
            $this->data['config']['paths']['Cache'] . DS . 'pages' . DS, 
            'all', 
            $MainMenu, 
            'getAll'
        );

        if ($_SERVER['REQUEST_URI'] != '/') {
            // remove trailing forward slash 
            $this->data['here'] = rtrim($_SERVER['REQUEST_URI'], '/'); 
        } else {
            $this->data['here'] = $_SERVER['REQUEST_URI'];
        }
        
        // if the query string includes page=1, remove it from $this->data['here']
        // so that the main menu can show the active page
        if (isset($_GET['page']) && $_GET['page'] == 1) {
            $this->data['here'] = strtok($_SERVER['REQUEST_URI'], '?'); // remove the query string
        }
    }
}
