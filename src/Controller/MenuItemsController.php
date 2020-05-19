<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Cache\Cache;

/**
 * MenuItems controller
 */
class MenuItemsController extends AppController
{
    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearCacheAction()
    {
        $cache = new Cache();
        $cache->clearCache([$this->data['config']['paths']['Cache'] . DS . 'menu-items' . DS]);
    }
}
