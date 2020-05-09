<?php

namespace App\Controller;

use Amicus\Controller\Controller;
use App\Model\MainMenu;
use Amicus\Configure\Configure;

/**
 * Pages controller
 */
class AppController extends Controller
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

        $configure = new Configure();
        
        $this->data['config'] = $configure->read();
        // avoid this being displayed in a view by accident
        unset($this->data['config']['Datasources']); 

        $this->data['main_menu_items'] = MainMenu::getAll();

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
