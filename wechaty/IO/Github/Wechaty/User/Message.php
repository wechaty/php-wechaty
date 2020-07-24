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
use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Schemas\MessagePayload;
use IO\Github\Wechaty\PuppetHostie\PuppetHostie;
use IO\Github\Wechaty\Util\Logger;

class Message extends Accessory {
    const AT_SEPRATOR_REGEX = "[\\u2005\\u0020]";

    /**
     * @var null|MessagePayload
     */
    protected ?MessagePayload $_payload = null;
    /**
     * @var null|PuppetHostie
     */
    protected $_puppet = null;

    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        $this->_puppet = $wechaty->getPuppet();
        parent::__construct($wechaty);
    }

    function getPayload() : MessagePayload {
        return $this->_payload;
    }

    function room() : ?Room {
        if($this->_payload == null){
            throw new WechatyException("no payload");
        }

        $roomId = $this->_payload->roomId;

        if (empty($roomId)) {
            return null;
        }
        return $this->wechaty->roomManager->load($roomId);
    }

    function say($something, Contact $contact = null) {
        $from = $this->from();
        $room = $this->room();

        $conversationId = "";

        if(!empty($room)) {
            $conversationId = $room->getId();
        } else if(!empty($from)) {
            $conversationId = $from->getId();
        } else {
            throw new WechatyException("neither room nor fromId?");
        }
        $msgId = "";

        if(gettype($something) == "string") {
            $msgId = $this->_puppet->messageSendText($conversationId, $something);
        } else if($something instanceof FileBox) {
            $msgId = $this->_puppet->messageSendFile($conversationId, $something);
        } else if($something instanceof UrlLink) {
            $msgId = $this->_puppet->messageSendUrl($conversationId, $something->getPayload());
        } else if($something instanceof MiniProgram) {
            $msgId = $this->_puppet->messageSendMiniProgram($conversationId, $something->getPayload());
        } else {
            throw new WechatyException("unknow message");
        }
        if(!empty($msgId)) {
            $msg = $this->wechaty->messageManager->load($msgId);
            return $msg;
        }
        return null;
    }

    function from() : ?Contact {
        if($this->_payload == null) {
            throw new WechatyException("no payload");
        }
        $fromId = $this->_payload->fromId ?: false;
        if(empty($fromId)) {
            return null;
        }

        return $this->wechaty->contactManager->load($fromId);
    }

    function to() : ?Contact {
        if($this->_payload == null){
            throw new WechatyException("no payload");
        }
        $toId = $this->_payload->toId ?: false;
        if(empty($toId)) {
            return null;
        }
        return $this->wechaty->contactManager->load($toId);
    }

    function recall() : bool {
        $this->_puppet->messageRecall($this->_id);
    }

    function type() : int {
        if($this->_payload == null){
            throw new WechatyException("no payload");
        }
        return $this->_payload->type ?: MessagePayload::MESSAGETYPE_UNKNOWN;
    }

    function self() : bool{
        $selfId = $this->_puppet->selfId();
        $from = $this->from();

        return $selfId == $from->getId();
    }

    function mentionList() : array {
        $room = $this->room();

        if ($room == null && $this->type() != MessagePayload::MESSAGETYPE_TEXT) {
            return array();
        }

        if (!empty($this->_payload->mentionIdList)) {
            $list = array();
            foreach ($this->_payload->mentionIdList as $value) {
                $contact = $this->wechaty->contactManager->load($value)->ready();
                $list[] = $contact;
            }
            return $list;
        }

        $atList = preg_split(self::AT_SEPRATOR_REGEX, $this->text());
        if (empty($atList)) {
            return array();
        }

        //TODO
        $rawMentionList = array();

        $mentionNameList = array();

        $roomMemberQueryFilter = new RoomMemberQueryFilter();
        $flatten = array();

        return $flatten;
    }

    function content() : String {
        return $this->text();
    }

    function text() : String {
        if($this->_payload == null){
            throw new WechatyException("no payload");
        }

        return $this->_payload->text ?:  "";
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