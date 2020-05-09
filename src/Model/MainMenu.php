<?php

namespace App\Model;

use Wruczek\PhpFileCache\PhpFileCache;

/**
 * Main menu model
 */
class MainMenu extends \Amicus\Model\Model
{
    /**
     * Get the main menu links from the database
     *
     * @return array
     */
    public static function getAll()
    {
        $cache = new PhpFileCache(static::getConfig()['paths']['Cache'] . DS . 'main-menu' . DS);

        if ($cache->isExpired("main_menu")) {
            $database = static::getDB();
            $main_menu = $database->select('main_menu', [
                'title',
                'url'
            ], [
                'published' => 1,
            ]);
            $expiration = 365*24*60*60;
            // permanent, this item will not be automatically cleared after expiring
            $cache->store("main_menu", $main_menu, $expiration, true); 
        }

        $main_menu = $cache->retrieve("main_menu");

        return $main_menu;
    }
}
