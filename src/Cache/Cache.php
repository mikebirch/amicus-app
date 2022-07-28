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
     * Retrieve data from cache
     *
     * @param string $path directory for cache
     * @param string $file file name for cache
     * @return array<mixed>
     */
    public function retrieve($path, $file)
    {
        $cache = new PhpFileCache($path, $file);
        return $cache->retrieve("result");
    }

    /**
     * Delete all files and folders in cache directories
     * Directus only has webhooks for create, update and delete which means that
     * you can’t target individual files via the clearCache() method in PhpFileCache
     * based on https://gist.github.com/mindplay-dk/a4aad91f5a4f1283a5e2#gistcomment-2036828
     *
     * @param array<int,string> $paths a list of directories
     * @return void
     */
    public function clearCache($paths)
    {
        foreach ($paths as $path) {
            // Handle bad arguments.
            if (empty($path) || !file_exists($path)) {
                return; // No such file/path exists.
            } elseif (is_file($path) || is_link($path)) {
                @unlink($path); // Delete file/link.
                return;
            }

            // Delete all children.
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                if (!@$action($fileinfo->getRealPath())) {
                    return; // Abort due to the failure.
                }
            }
        }
    }
}
