<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:09 PM
 */

namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Exceptions\WechatyException;
use IO\Github\Wechaty\Puppet\Schemas\MessagePayload;
use IO\Github\Wechaty\PuppetHostie\PuppetHostie;
use IO\Github\Wechaty\Util\Logger;

class Message extends Accessory {
    const AT_SEPRATOR_REGEX = "[\\u2005\\u0020]";

    /**
     * @var null|MessagePayload
     */
    protected $_payload = null;
    /**
     * @var null|PuppetHostie
     */
    protected $_puppet = null;

    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        $this->_puppet = $wechaty->getPuppet();
        parent::__construct($wechaty);
    }

    function room() : Room {
        if($this->_payload == null){
        throw new WechatyException("no payload");
        }

        $roomId = $this->_payload->roomId;

        if (empty($roomId)) {
            return false;
        }
        return $this->wechaty->roomManager->load($roomId);
    }

    function ready() : void {
        if ($this->isReady()) {
            return;
        }

        $this->_payload = $this->_puppet->messagePayload($this->_id);

        Logger::DEBUG("message payload is {}",$this->_payload);

        if ($this->_payload == null) {
            throw new WechatyException("no playload");
        }

        $fromId = $this->_payload->fromId;
        $roomId = $this->_payload->roomId;
        $toId = $this->_payload->toId;

        if (!empty($roomId)) {
            $this->wechaty->roomManager->load($roomId)->ready();
        }

        if (!empty($fromId)) {
            $this->wechaty->contactManager->load($fromId)->ready();
        }

        if (!empty($toId)) {
            $this->wechaty->contactManager->load($toId)->ready();
        }
    }

    function isReady() : bool {
        return $this->_payload != null;
    }
}