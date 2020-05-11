<?php

namespace App\Model;

use Wruczek\PhpFileCache\PhpFileCache;
use PDO;

/**
 * Main menu model
 */
class MainMenu extends \Showus\Model\Model
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
            $pdo = static::getPDO();
            $stmt = $pdo->query('SELECT title, url FROM main_menu WHERE published = 1');
            $main_menu = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $expiration = 365*24*60*60;
            // permanent, this item will not be automatically cleared after expiring
            $cache->store("main_menu", $main_menu, $expiration, true); 
        }

        $main_menu = $cache->retrieve("main_menu");

        return $main_menu;
    }
}
