<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:23 PM
 */
namespace IO\Github\Wechaty;

use IO\Github\Wechaty\Puppet\EventEmitter\EventEmitter;
use IO\Github\Wechaty\Puppet\Puppet;
use IO\Github\Wechaty\Puppet\Schemas\Event\EventScanPayload;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\Schemas\WechatyOptions;
use IO\Github\Wechaty\Puppet\StateEnum;
use IO\Github\Wechaty\User\Friendship;
use IO\Github\Wechaty\User\Manager\ContactManager;
use IO\Github\Wechaty\User\Manager\MessageManager;
use IO\Github\Wechaty\User\Manager\RoomInvitationManager;
use IO\Github\Wechaty\User\Manager\RoomManager;
use IO\Github\Wechaty\Util\Console;
use IO\Github\Wechaty\Util\Logger;
use LM\Exception;

class Wechaty extends EventEmitter {

    private $_puppetOptions = null;
    private $_wechatyOptions = null;

    private $_status = StateEnum::OFF;
    private $_readyState = StateEnum::OFF;

    /**
     * @var null|ContactManager
     */
    public $contactManager = null;

    /**
     * @var null | MessageManager
     */
    public $messageManager = null;

    /**
     * @var null | RoomManager
     */
    public $roomManager = null;

    /**
     * @var null | RoomInvitationManager
     */
    public $roomInvitationManager = null;

    /**
     * @var null|PuppetHostie\PuppetHostie
     */
    private $_puppet = null;

    /**
     * Wechaty constructor.
     * @param $wechatyOptions \IO\Github\Wechaty\Puppet\Schemas\WechatyOptions
     */
    public function __construct($wechatyOptions) {
        $this->_wechatyOptions = $wechatyOptions;
        $this->_puppetOptions = $wechatyOptions->puppetOptions;
    }

    public static function getInstance($token, $endPoint = "") {
        $puppetOptions = new PuppetOptions();
        $puppetOptions->token = $token;
        $puppetOptions->endPoint = $endPoint;
        $wechatyOptions = new WechatyOptions();
        $wechatyOptions->puppetOptions = $puppetOptions;
        return new Wechaty($wechatyOptions);
    }

    public function start() : Wechaty {
        $this->_initPuppet();
        echo "start Wechaty\n";
        try {
            $this->_puppet->start();
            $status = StateEnum::ON;
            $this->emit(EventEnum::START, "");

            //addHook();
        } catch (\Exception $e) {
            echo "service stopped with exception, " . $e->getMessage();
            Logger::ERR(array("service stopped with exception"), $e);
        }
        return $this;
    }

    public function stop() {
        $this->_puppet->stop();
    }

    private function _initPuppet() {
        if($this->_puppet) {
            return;
        }

        $this->_puppet = new PuppetHostie\PuppetHostie($this->_puppetOptions);

        $this->_initPuppetEventBridge($this->_puppet);
        $this->_initPuppetAccessory($this->_puppet);
    }

    function onScan($listener) : Wechaty {
        return $this->_on(EventEnum::SCAN, $listener);
    }

    function onHeartBeat($listener) : Wechaty {
        return $this->_on(EventEnum::HEART_BEAT, $listener);
    }

    function onLogin($listener) : Wechaty {
        return $this->_on(EventEnum::LOGIN, $listener);
    }

    function onRoomJoin($listener):Wechaty {
        return $this->_on(EventEnum::ROOM_JOIN, $listener);
    }

    function onRoomLeave($listener):Wechaty {
        return $this->_on(EventEnum::ROOM_LEAVE, $listener);
    }

    function onRoomTopic($listener):Wechaty {
        return $this->_on(EventEnum::ROOM_TOPIC, $listener);
    }

    function onMessage($listener):Wechaty{
        return $this->_on(EventEnum::MESSAGE, $listener);
    }

    function getPuppet() : Puppet {
        return $this->_puppet;
    }

    function friendship() : Friendship {
        return new Friendship($this);
    }

    private function _on($event, \Closure $listener) : Wechaty {
        $this->on($event, $listener);
        return $this;
    }

    private function _initPuppetEventBridge(PuppetHostie\PuppetHostie $puppet) {
        //{"qrcode":"https://login.weixin.qq.com/l/IaysbZa04Q==","status":5}
        $puppet->on(EventEnum::SCAN, function(EventScanPayload $payload) {
            $this->emit(EventEnum::SCAN, $payload->qrcode ?: "", $payload->status, $payload->data ?: "");
        });
        $puppet->on(EventEnum::HEART_BEAT, function($payload) {
            $this->emit(EventEnum::HEART_BEAT, $payload["data"]);
        });
        $puppet->on(EventEnum::DONG, function($payload) {
            $this->emit(EventEnum::DONG, $payload["data"]);
        });
        $puppet->on(EventEnum::ERROR, function($payload) {
            $this->emit(EventEnum::ERROR, $payload["data"]);
        });
        $puppet->on(EventEnum::FRIENDSHIP, function($payload) {
            $friendship = $this->friendship();
            $friendship->load($payload["friendshipId"]);
            $friendship->ready();
            $this->emit(EventEnum::FRIENDSHIP, $friendship);
        });
        $puppet->on(EventEnum::LOGIN, function($payload) {
            $contact = $this->contactManager->loadSelf($payload["contactId"]);
            $contact->ready();
            $this->emit(EventEnum::LOGIN, $contact);
        });
        $puppet->on(EventEnum::LOGOUT, function($payload) {
            $contact = $this->contactManager->loadSelf($payload["contactId"]);
            $contact->ready();
            $this->emit(EventEnum::LOGOUT, $contact, $payload["data"]);
        });
        $puppet->on(EventEnum::MESSAGE, function($payload) {
            $msg = $this->messageManager->load($payload["messageId"]);
            $msg->ready();
            $this->emit(EventEnum::MESSAGE, $msg);

            $room = $msg->room();
            if($room) {
                $room->emit(EventEnum::MESSAGE, $msg);
            }
        });
        $puppet->on(EventEnum::READY, function($payload) {
            $this->emit(EventEnum::READY, $payload);

            $this->_readyState = StateEnum::ON;
        });
        $puppet->on(EventEnum::ROOM_INVITE, function($payload) {
            $roomInvitation = $this->roomInvitationManager->load($payload["roomInvitationId"]);
            $this->emit(EventEnum::ROOM_INVITE, $roomInvitation);
        });
        $puppet->on(EventEnum::ROOM_JOIN, function($payload) {
            $room = $this->roomManager->load($payload["roomId"]);

            $inviteeList = array();
            $inviteeListId = $payload["inviteeIdList"];
            foreach($inviteeListId as $value) {
                $contact = $this->contactManager->loadSelf($value);
                $contact->ready();
                $inviteeList[] = $contact;
            }

            $inviter = $this->contactManager->loadSelf($payload["inviterId"]);
            $inviter->ready();

            $time = $payload["timestamp"];
            $this->emit(EventEnum::ROOM_JOIN, $room, $inviteeList, $inviter, $time);
            $room->emit(EventEnum::JOIN, $inviteeList, $inviter, $time);
        });
        $puppet->on(EventEnum::ROOM_LEAVE, function($payload) {
            $this->emit(EventEnum::ROOM_LEAVE, $payload);
        });
        $puppet->on(EventEnum::ROOM_TOPIC, function($payload) {
            $this->emit(EventEnum::ROOM_TOPIC, $payload);
        });
    }

    private function _initPuppetAccessory(PuppetHostie\PuppetHostie $puppet) {
        $this->contactManager = new ContactManager($this);
        $this->messageManager = new MessageManager($this);
        $this->roomManager = new RoomManager($this);
        $this->roomInvitationManager = new RoomInvitationManager($this);
    }
}