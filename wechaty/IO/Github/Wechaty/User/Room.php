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
use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\Query\RoomMemberQueryFilter;
use IO\Github\Wechaty\Puppet\Schemas\RoomPayload;
use IO\Github\Wechaty\PuppetHostie\PuppetHostie;
use IO\Github\Wechaty\Util\Logger;
use IO\Github\Wechaty\Util\QrcodeUtils;

class Room extends Accessory {
    const FOUR_PER_EM_SPACE = "\u2005";

    /**
     * @var null|RoomPayload
     */
    protected ?RoomPayload $_payload = null;
    /**
     * @var null|PuppetHostie
     */
    protected $_puppet = null;

    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        $this->_puppet = $wechaty->getPuppet();
        parent::__construct($wechaty);
    }

    function getPayload() : RoomPayload {
        return $this->_payload;
    }

    function isReady() : bool {
        return $this->_payload != null;
    }

    function sync() : void {
        $this->ready(true);
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
        // cache contact ?
        $memberIdList = $this->_puppet->roomMemberList($this->_id);
        foreach($memberIdList as $value) {
            $this->wechaty->contactManager->load($value)->ready();
        }
    }

    function say($something, $varList = array()) {
        $msgId = "";
        $text = "";

        if(gettype($something) == "string") {
            $mentionList = array();
            if(!empty($varList)) {
                $list = $varList[0];
                if(gettype($list) != "array") {
                    throw new WechatyException("room say contact args not valid");
                }
                foreach($list as $value) {
                    if(!$value instanceof Contact) {
                        throw new WechatyException("mentionList must be contact when not using String array function call.");
                    }
                }
                $mentionList = $list;
                $mentionAlias = [];
                foreach($mentionList as $contact) {
                    $alias = $this->alias($contact);
                    if(!empty($alias)) {
                        $concatText = $alias;
                    } else {
                        $concatText = $contact->name();
                    }
                    $mentionAlias[] = "@$concatText";
                }
                $mentionText = implode(self::FOUR_PER_EM_SPACE, $mentionAlias);
                $text = $mentionText;
            } else {
                $text = $something;
            }
            $mentionIds = array();
            foreach($mentionList as $value) {
                $mentionIds[] = $value->getId();
            }
            $msgId = $this->_puppet->messageSendText($this->_id, $something, $mentionIds);
        } else if($something instanceof FileBox) {
            $msgId = $this->_puppet->messageSendFile($this->_id, $something);
        } else if($something instanceof Contact) {
            $msgId = $this->_puppet->messageSendContact($this->_id, $something->getId());
        } else if($something instanceof UrlLink) {
            $msgId = $this->_puppet->messageSendUrl($this->_id, $something->getPayload());
        } else if($something instanceof MiniProgram) {
            $msgId = $this->_puppet->messageSendMiniProgram($this->_id, $something->getPayload());
        } else {
            throw new WechatyException("unknow message");
        }

        if ($msgId != null) {
            $msg = $this->wechaty->messageManager->load($msgId);
            return $msg;
        }

        return null;
    }

    function alias(Contact $contact) : String {
        $roomMemberPayload = $this->wechaty->getPuppet()->roomMemberPayload($this->_id, $contact->getId());

        return $roomMemberPayload->roomAlias;
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

    function add(Contact $contact) : void {
        $this->_puppet->roomAdd($this->_id, $contact->getId());
    }

    function del(Contact $contact): void {
        $this->_puppet->roomDel($this->_id, $contact->getId());
    }

    function quit(): void {
        $this->_puppet->roomQuit($this->_id);
    }

    function getTopic(): String {
        if (!$this->isReady()) {
            Logger::WARNING("Room topic() room not ready");
            throw new WechatyException("not ready");
        }

        if ($this->_payload != null && $this->_payload->topic != null) {
            return $this->_payload->topic;
        } else {
            $memberIdList = $this->_puppet->roomMemberList($this->_id);
            $that = $this;
            $memberList = array_map(function($value) use ($that) {
                return $that->wechaty->contactManager->load($value);
            }, array_filter($memberIdList, function($value) use ($that) {
                return $value != $that->_puppet->selfId();
            }));

            $defaultTopic = "";
            if (!empty($memberList)) {
                $defaultTopic = $memberList[0]->name();
            }

            if (count($memberList) >= 2) {
                for($i = 1 ; $i <= 2 ; $i++) {
                    $defaultTopic .= ",{$memberList[$i]->name()}";
                }
            }
            return $defaultTopic;
        }
    }

    function setTopic(String $newTopic) : void {
        if (!$this->isReady()) {
            Logger::WARNING("Room topic() room not ready");
            throw new WechatyException("not ready");
        }
        try {
            $this->_puppet->setRoomTopic($this->_id, $newTopic);
        } catch(\Exception $e) {
            Logger::WARNING("Room topic(newTopic=$newTopic) exception:$e");
            throw new WechatyException($e);
        }
    }

    function announce(?String $text = "") {
        if(empty($text)) {
            return $this->_puppet->getRoomAnnounce($this->_id);
        } else {
            return $this->_puppet->setRoomAnnounce($this->_id, $text);
        }
    }

    /**
     * 该微信号在 2020-07-26 23:05 生成的群二维码，因使用了微信外挂、非官方客户端或模拟器等违规行为，二维码已经失效。
     *
     * @return String
     * @throws WechatyException
     */
    function qrCode(): String {
        throw new WechatyException("not support");
        $qrCodeValue = $this->_puppet->roomQRCode($this->_id);
        return QrcodeUtils::guardQrCodeValue($qrCodeValue);
    }

    function memberAll(RoomMemberQueryFilter $query): array {
        if ($query == null) {
            return $this->memberList();
        }

        $contactIdList = $this->wechaty->getPuppet()->roomMemberSearch($this->_id, $query);
        $contactList = array_map(function($value) {
            return $this->wechaty->contactManager->load($value);
        }, $contactIdList);

        return $contactList;

    }

    function memberList(): array {
        $memberIdList = $this->wechaty->getPuppet()->roomMemberList($this->_id);

        if (empty($memberIdList)) {
            return array();
        }

        $contactList = array_map(function($value) {
            return $this->wechaty->contactManager->load($value);
        }, $memberIdList);
        return $contactList;
    }

    private function _on($eventName, $listener) : Room {
        /*parent::on($eventName, function($contact, $roomInvitation) use ($listener) {
            call_user_func($listener, $contact, $roomInvitation);
        });*/
        parent::on($eventName, $listener);
        return $this;
    }
}