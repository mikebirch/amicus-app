<?php

namespace App\Controller;

use App\Controller\AppController;
use Paulus\Cache\Cache;

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
        $cache->clearCache(['main-menu' . DS]);
    }
}
