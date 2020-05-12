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
     * @param BlogPosts|Pages $model an instance of the model
     * @param string $action the method in the model
     * @param string $params the parameters for the method 
     * @return array
     */
    public function cacheData($path, $file, $model, $action, $params = null)
    {
        $cache = new PhpFileCache($path, $file );
        if ($cache->isExpired("result")) {
            $result = $model->$action($params);
            $cache->store("result", $result, null, true); 
        }
        return $cache->retrieve("result");
    }

    /**
     * Delete all files in cache directories
     * Directus only has webhooks for create, update and delete which means that
     * you can’t target individual files via the clearCache() method in PhpFileCache
     *
     * @param array $paths a list of directories
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
