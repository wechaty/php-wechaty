<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 10:50 PM
 */
namespace IO\Github\Wechaty\Puppet\Cache;

class CacheFactory {
    const DEFAULT_CACHE = "YAC";
    const CACHE_YAC = "yac";
    const CACHE_ARRAY = "array";
    const CACHE_LIBSHMCACHE = "libshmcache";

    /**
     * @param string $type
     * @param string $name
     * @return Cache|Yac|ArrayCache
     */
    public static function getCache($type = self::CACHE_ARRAY, $name = "") {
        if($type == self::CACHE_YAC) {
            return Yac::getInstance();
        } elseif($type == self::CACHE_ARRAY) {
            if(!empty($name)) {
                return ArrayCache::getInstanceWithName($name);
            } else {
                return ArrayCache::getInstance();
            }
        }

        return Cache::getInstance();
    }
}