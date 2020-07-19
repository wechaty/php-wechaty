<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 5:37 PM
 */
namespace IO\Github\Wechaty\Puppet;

use IO\Github\Wechaty\Puppet\Cache\CacheFactory;
use IO\Github\Wechaty\Puppet\EventEmitter\EventEmitter;
use IO\Github\Wechaty\Puppet\Exceptions\InvalidArgumentException;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Util\Logger;
use LM\Exception;

abstract class Puppet extends EventEmitter {
    protected static $_STATE = StateEnum::OFF;

    protected $_puppetOptions = null;
    /**
     * @var Cache\Cache|Cache\Yac|null
     */
    protected $_cache = null;

    const CACHE_CONTACT_PAYLOAD_PREFIX = "ccp_";
    const CACHE_FRIENDSHIP_PAYLOAD_PREFIX = "cfp_";
    const CACHE_MESSAGE_PAYLOAD_PREFIX = "cmp_";
    const CACHE_ROOM_PAYLOAD_PREFIX = "crp_";
    const CACHE_ROOM_MEMBER_PAYLOAD_PREFIX = "crmp_";
    const CACHE_ROOM_INVITATION_PAYLOAD_PREFIX = "crip_";

    public function __construct(PuppetOptions $puppetOptions) {
        if(empty($puppetOptions->token)) {
            throw new InvalidArgumentException("token is null");
        }
        $this->_puppetOptions = $puppetOptions;

        $this->_cache = $this->_initCache();
    }

    protected function _initCache() {
        return CacheFactory::getCache();
    }

    abstract public function start();
    abstract public function stop();
}