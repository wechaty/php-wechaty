<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:06 AM
 */
namespace IO\Github\Wechaty;

use IO\Github\Wechaty\Puppet\Cache\CacheFactory;
use IO\Github\Wechaty\Puppet\EventEmitter\EventEmitter;

class Accessory extends EventEmitter {
    protected $_cache = null;

    protected $_id = null;

    /**
     * @var Wechaty
     */
    public $wechaty;

    public function __construct($wechaty) {
        $this->wechaty = $wechaty;
    }

    protected function _initCache() {
        return CacheFactory::getCache();
    }
}