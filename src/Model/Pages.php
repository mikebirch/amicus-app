<?php

namespace App\Model;

use Wruczek\PhpFileCache\PhpFileCache;
use PDO;

/**
 * Pages model
 */
class Pages extends \Anticus\Model\Model
{   
    /**
     * Get all the published pages from the database
     *
     * @return array<mixed>
     */
    public static function getAll()
    {    
        $pdo = static::getPDO();
        $stmt = $pdo->query(
            'SELECT url 
            FROM pages 
            WHERE published = 1'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a page from the database
     *
     * @param string $url the page’s url
     * @return array<mixed>
     */
    public static function getByUrl($url)
    { 
        $pdo = static::getPDO();
        $stmt = $pdo->prepare(
            'SELECT 
            id,
            title,
            url,
            body,
            meta_title,
            meta_description,
            created 
            FROM pages 
            WHERE published = 1 AND url = ?'
        );
        $stmt->execute([$url]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
