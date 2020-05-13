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
     * @return array<mixed>
     */
    public static function getAll()
    {
        $pdo = static::getPDO();
        $stmt = $pdo->query('SELECT title, url FROM main_menu WHERE published = 1');
        $main_menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $main_menu;
    }
}
