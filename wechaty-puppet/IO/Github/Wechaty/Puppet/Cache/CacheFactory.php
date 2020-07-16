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
    const CACHE_LIBSHMCACHE = "libshmcache";

    /**
     * @param string $type
     * @return Cache|Yac
     */
    public static function getCache($type = self::CACHE_YAC) {
        if($type == self::CACHE_YAC) {
            return Yac::getInstance();
        }

        return Cache::getInstance();
    }
}