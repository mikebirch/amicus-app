<?php

namespace App\Model;

use Wruczek\PhpFileCache\PhpFileCache;
use PDO;

/**
 * Main menu model
 */
class MenuItems extends \Anticus\Model\Model
{
    /**
     * Get the main menu links from the database
     *
     * @return array<mixed>
     */
    public static function getAll()
    {
        $pdo = static::getPDO();
        $stmt = $pdo->query(
            'SELECT menu, title, url, sort 
            FROM menu_items 
            WHERE published = 1 
            ORDER by sort ASC'
        );
        $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $menu_items;
    }
}
