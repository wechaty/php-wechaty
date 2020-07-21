<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 12:57 PM
 */
namespace IO\Github\Wechaty\Puppet\Cache;

class ArrayCache extends Cache {

    public static function getInstanceWithName($name) {
        if(isset(self::$_INSTANCES[$name]) && !empty(self::$_INSTANCES[$name])) {
            self::$_INSTANCES[$name] = new self();
        }
        return self::$_INSTANCES[$name];
    }

    public function __construct() {
        $this->_cacheInstance = array();
    }

    public function get($key) {
        if(isset($this->_cacheInstance[$key])) {
            return $this->_cacheInstance[$key];
        } else {
            return false;
        }
    }

    public function set($key, $value) {
        return $this->_cacheInstance[$key] = $value;
    }

    public function delete($key) {
        unset($this->_cacheInstance[$key]);
        return true;
    }
}