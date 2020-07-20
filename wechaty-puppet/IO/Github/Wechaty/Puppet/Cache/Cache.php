<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 10:44 PM
 */
namespace IO\Github\Wechaty\Puppet\Cache;

class Cache {
    protected $_cacheInstance;
    protected static $_INSTANCES = array();

    public static function getInstance() {
        static $_instance = NULL;
        if (empty($_instance)) {
            $_instance = new static();
        }
        return $_instance;
    }

    public function get($key) {

    }

    public function set($key, $value) {

    }
}