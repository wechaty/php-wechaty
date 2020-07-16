<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 5:37 PM
 */
namespace IO\Github\Wechaty\Puppet;

use IO\Github\Wechaty\Puppet\Cache\CacheFactory;
use IO\Github\Wechaty\Puppet\Exceptions\InvalidArgumentException;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;

class Puppet {
    protected $_puppetOptions = null;

    const CACHE_CONTACT_PAYLOAD_PREFIX = "ccp_";
    const CACHE_FRIENDSHIP_PAYLOAD = "cfp_";
    const CACHE_MESSAGE_PAYLOAD = "cmp_";
    const CACHE_ROOM_PAYLOAD = "crp_";
    const CACHE_ROOM_MEMBER_PAYLOAD = "crmp_";
    const CACHE_ROOM_INVITATION_PAYLOAD = "crip_";

    public function __construct(PuppetOptions $puppetOptions) {
        if(empty($puppetOptions->token)) {
            throw new InvalidArgumentException("token is null");
        }
        $this->_puppetOptions = $puppetOptions;
    }
}