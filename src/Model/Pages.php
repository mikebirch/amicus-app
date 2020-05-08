<?php

namespace App\Model;

use Wruczek\PhpFileCache\PhpFileCache;

/**
 * Pages model
 */
class Pages extends \Paulus\Model\Model
{   
    /**
     * Get all the published pages from the database
     *
     * @return array
     */
    public static function getAll()
    {    
        $database = static::getDB();
        $pages = $database->select('pages', [
            'url'
        ], [
            'published' => 1,
        ]);

        return $pages;
    }

    /**
     * Get a page from the database
     *
     * @return array
     */
    public static function getByUrl($url)
    { 
        $database = static::getDB();
        $page = $database->get('pages', [
            'id',
            'title',
            'url',
            'body',
            'meta_title',
            'meta_description',
            'created'
        ], [
            'url' => $url,
            'published' => 1
        ]);

        return ['page' => $page];
    }
}
