<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Cache\Cache;

/**
 * MainMenu controller
 */
class MainMenuController extends AppController
{
    /**
     * Used by webhooks to clear cache
     *
     * @return void
     */
    public function clearCacheAction()
    {
        $cache = new Cache();
        $cache->clearCache([$this->data['config']['paths']['Cache'] . DS . 'main-menu' . DS]);
    }
}
