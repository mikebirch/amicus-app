<?php

namespace App\Cache;

use Wruczek\PhpFileCache\PhpFileCache;

/**
 * Cache using PhpFileCache
 */
class Cache
{   
    /**
     * Cache the data returned by a model’s method
     *
     * @param string $path directory for cache
     * @param string $file file name for cache
     * @param string $model namespace of the model class
     * @param string $action the method in the model
     * @param mixed $params the parameters for the method 
     * @return array<mixed> model data
     */
    public function cacheData($path, $file, $model, $action, $params = null)
    {
        $cache = new PhpFileCache($path, $file );
        if ($cache->isExpired("result")) {
            $result = $model::$action($params);
            if ( !empty($result) ) {
                $cache->store("result", $result, 365*24*60*60, true); 
            }
        }
        return $cache->retrieve("result");
    }

    /**
     * Delete all files in cache directories
     * Directus only has webhooks for create, update and delete which means that
     * you can’t target individual files via the clearCache() method in PhpFileCache
     *
     * @param array<int,string> $paths a list of directories
     * @return void
     */
    public function clearCache($paths)
    {
        foreach ($paths as $path) {
            foreach (new \DirectoryIterator($path) as $fileInfo) {
                if(!$fileInfo->isDot()) {
                    unlink($fileInfo->getPathname());
                }
            }
        }
    }
}
