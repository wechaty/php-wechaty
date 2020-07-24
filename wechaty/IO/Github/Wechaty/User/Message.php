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
use IO\Github\Wechaty\Puppet\Schemas\RoomMemberQueryFilter;
use IO\Github\Wechaty\PuppetHostie\PuppetHostie;
use IO\Github\Wechaty\Util\Logger;

class Message extends Accessory {
    const AT_SEPRATOR_REGEX = "[\\u2005\\u0020]";
    const SPECIAL_REGEX_CHARS = "[{}()\\[\\].+*?^$\\\\|]";

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
        if($this->_payload == null) {
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
        if($this->_payload == null) {
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
        if($this->_payload == null) {
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

    function talker() : ?Contact {
        return $this->from();
    }

    function toRecalled() : ?Message {
        if($this->type() != MessagePayload::MESSAGETYPE_RECALLED) {
            throw new WechatyException("Cannot call  toRecalled() on message which id not recalled type");
        }

        $originalMessageId = $this->text();

        if(empty($originalMessageId)) {
            throw new WechatyException("Cannot find recalled Message");
        }

        try {
            $message = $this->wechaty->messageManager->load($originalMessageId);
            $message->ready();
            return $message;
        } catch (\Exception $e) {
            Logger::WARNING("Can not retrieve the recalled message with id ${originalMessageId}");
        }
        return null;
    }

    function mentionText() : String {
        $text = $this->text();
        $room = $this->room();

        $mentionList = $this->mentionList();
        if($room == null || empty($mentionList)){
            return $text;
        }
        $mentionNameList = array();
        foreach($mentionList as $value) {
            $alias = $room->alias($value);
            $name = $value->name();
            if(!empty($alias)) {
                $toAliasName = $alias;
            } else {
                $toAliasName = $name;
            }
            $mentionNameList[] = $toAliasName;
        }

        $textWithoutMention = $text;
        foreach($mentionNameList as $value) {
            $escapedCur = $this->escapeRegExp($value);
            $regex = "@${escapedCur}(\\u2005|\\u0020|\$)";
            $textWithoutMention = preg_replace($regex, "", $text);
        }
        return trim($textWithoutMention);
    }

    function mentionSelf() : bool {
        $selfId = $this->_puppet->selfId();
        $mentionList = $this->mentionList();

        foreach($mentionList as $value) {
            if($value->getId() == $selfId) {
                return true;
            }
        }

        return false;
    }

    function file() : FileBox {
        return $this->toFileBox();
    }

    function toImage() : Image {
        if($this->type() != MessagePayload::MESSAGETYPE_IMAGE) {
            throw new WechatyException("not a image type, type is " . $this->type());
        }
        return $this->wechaty->imageManager->create($this->_id);
    }

    function toContact() : Contact {
        if($this->type() != MessagePayload::MESSAGETYPE_CONTACT) {
            throw new WechatyException("message not a ShareCard");
        }

        $contactId = $this->wechaty->getPuppet()->messageContact($this->_id);
        if(empty($contactId)){
            throw new WechatyException("can not get contact id by message {$this->_id}");
        }

        $contact = $this->wechaty->contactManager->load($contactId);
        $contactId->ready();
        return $contact;
    }

    function toUrlLink() : UrlLink {
        if($this->type() != MessagePayload::MESSAGETYPE_URL) {
            throw new WechatyException("message not a Url Link");
        }

        $urlPayload = $this->wechaty->getPuppet()->messageUrl($this->_id);
        return new UrlLink($urlPayload);
    }

    function toMiniProgram():MiniProgram{
        if($this->type() != MessagePayload::MESSAGETYPE_MINIPROGRAM) {
            throw new WechatyException("message not a MiniProgram");
        }

        $miniProgramPayload = $this->wechaty->getPuppet()->messageMiniProgram($this->_id);
        return new MiniProgram($miniProgramPayload);
    }

    function toFileBox() : FileBox {
        if($this->type() != MessagePayload::MESSAGETYPE_TEXT) {
            throw new WechatyException("text message no file");
        }
        return $this->wechaty->getPuppet()->messageFile($this->_id);
    }

    public function __toString() {
        return "Message(payload=$this->_payload,id=$this->_id)";
    }

    function multipleAt(String $str) : array {
        $re = "^.*?@";
        $str1 = preg_replace($re, "@", $str);

        $name = "";
        $nameList = array();
        $mentionNames = array_reverse(array_filter(explode("@", $str1), function($value) {
            return !empty($value);
        }));
        foreach($mentionNames as $mentionName) {
            $name = "$mentionName@$name";
            $nameList[] = substr($name, 0, strlen($name) - 1);
        }
        return $nameList;
    }

    function escapeRegExp(String $str) : ?String {
        return preg_replace(self::SPECIAL_REGEX_CHARS, "\\\\$0", $str);
    }
}