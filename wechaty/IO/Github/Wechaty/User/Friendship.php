<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:04 AM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Exceptions\WechatyException;
use IO\Github\Wechaty\Puppet\Schemas\FriendshipPayload;

class Friendship extends Accessory {
    /**
     * @var null|FriendshipPayload
     */
    private $_payload = null;

    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        parent::__construct($wechaty);
    }

    function load(String $id) : Friendship {
        $this->_id = $id;
        return $this;
    }

    function isReady(): bool {
        return $this->_payload != null;
    }

    function ready() {
        if($this->isReady()) {
            return true;
        }
        $this->_payload = $this->wechaty->getPuppet()->friendshipRawPayload($this->_id);
        $this->contact()->ready();
    }

    function contact() : Contact {
        if($this->_payload == null) {
            throw new WechatyException("no payload");
        }
        return $this->wechaty->contactManager->load($this->_payload->contactId);
    }
}