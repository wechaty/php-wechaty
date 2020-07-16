<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 10:44 PM
 */
namespace IO\Github\Wechaty\Puppet\Cache;

class Yac extends Cache {
    public function __construct() {
        $this->_cacheInstance = new \Yac("wechaty_");
    }

    public function get($key) {
        return $this->_cacheInstance->get($key);
    }

    public function set($key, $value) {
        return $this->_cacheInstance->set($key, $value);
    }
}