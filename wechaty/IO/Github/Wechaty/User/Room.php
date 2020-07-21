<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 3:17 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Exceptions\WechatyException;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\RoomPayload;
use IO\Github\Wechaty\PuppetHostie\PuppetHostie;
use IO\Github\Wechaty\Util\Logger;

class Room extends Accessory {
    /**
     * @var null|RoomPayload
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

    function isReady() : bool {
        return $this->_payload != null;
    }

    function ready(bool $forceSync = false) : void {
        if(!$forceSync && $this->isReady()) {
            return;
        }

        if($forceSync) {
            $this->_puppet->roomPayloadDirty($this->_id);
            $this->_puppet->roomMemberPayloadDirty($this->_id);
        }

        $this->_payload = $this->_puppet->roomPayload($this->_id);
        Logger::DEBUG("get room payload", array("payload" => $this->_payload, "id" => $this->_id));
        if($this->_payload == null) {
            throw new WechatyException("no payload");
        }
        $memberIdList = $this->_puppet->roomMemberList($this->_id);
        foreach($memberIdList as $value) {
            $this->wechaty->contactManager->load($value)->ready();
        }
    }

    function onInvite($listener) : Room {
        // $contact $roomInvitation
        return $this->_on(EventEnum::INVITE, $listener);
    }

    function onLeave($listener) : Room {
        // $contact_array $contact $date
        return $this->_on(EventEnum::LEAVE, $listener);
    }

    function onInnerMessage($listener) : Room {
        // $message $date
        return $this->_on(EventEnum::MESSAGE, $listener);
    }

    function onJoin($listener) : Room {
        // $contact_array $contact $date
        return $this->_on(EventEnum::JOIN, $listener);
    }

    function onTopic($listener) : Room {
        // $string $string $contact $date
        return $this->_on(EventEnum::TOPIC, $listener);
    }

    private function _on($eventName, $listener) : Room {
        /*parent::on($eventName, function($contact, $roomInvitation) use ($listener) {
            call_user_func($listener, $contact, $roomInvitation);
        });*/
        parent::on($eventName, $listener);
        return $this;
    }
}