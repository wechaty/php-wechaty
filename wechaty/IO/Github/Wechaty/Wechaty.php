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
use IO\Github\Wechaty\Puppet\Schemas\Date;
use IO\Github\Wechaty\Puppet\Schemas\Event\EventScanPayload;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\Schemas\WechatyOptions;
use IO\Github\Wechaty\Puppet\StateEnum;
use IO\Github\Wechaty\Puppet\MemoryCard\MemoryCard;
use IO\Github\Wechaty\User\ContactSelf;
use IO\Github\Wechaty\User\Friendship;
use IO\Github\Wechaty\User\Manager\ContactManager;
use IO\Github\Wechaty\User\Manager\FriendshipManager;
use IO\Github\Wechaty\User\Manager\ImageManager;
use IO\Github\Wechaty\User\Manager\MessageManager;
use IO\Github\Wechaty\User\Manager\RoomInvitationManager;
use IO\Github\Wechaty\User\Manager\RoomManager;
use IO\Github\Wechaty\User\Manager\TagManager;
use IO\Github\Wechaty\Util\Console;
use IO\Github\Wechaty\Util\Logger;
use LM\Exception;

class Wechaty extends EventEmitter {

    protected static $_INSTANCE;
    protected static $_INSTANCES = array();

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
     * @var null | TagManager
     */
    public $tagManager = null;

    /**
     * @var null | ImageManager
     */
    public ?ImageManager $imageManager = null;

    /**
     * @var null | FriendshipManager
     */
    public ?FriendshipManager $friendshipManager = null;

    /**
     * @var null|PuppetHostie\PuppetHostie
     */
    private $_puppet = null;

    private $_memoryCard = null;

    /**
     * Wechaty constructor.
     * @param $wechatyOptions \IO\Github\Wechaty\Puppet\Schemas\WechatyOptions
     */
    public function __construct($wechatyOptions) {
        $this->_wechatyOptions = $wechatyOptions;
        $this->_puppetOptions = $wechatyOptions->puppetOptions;
    }

    /**
     * @param $token
     * @param string $endPoint
     * @return Wechaty
     */
    public static function getInstance($token, $endPoint = "") {
        $key = md5($token . $endPoint);
        if(isset(self::$_INSTANCES[$key]) && !empty(self::$_INSTANCES[$key])) {
            return self::$_INSTANCES[$key];
        } else {
            $puppetOptions = new PuppetOptions();
            $puppetOptions->token = $token;
            $puppetOptions->endPoint = $endPoint;
            $wechatyOptions = new WechatyOptions();
            $wechatyOptions->puppetOptions = $puppetOptions;
            $wechaty = new Wechaty($wechatyOptions);
            self::$_INSTANCES[$key] = $wechaty;
            return self::$_INSTANCES[$key];
        }
    }

    public function start() : Wechaty {
        $this->_memoryCard = new MemoryCard();
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

    function onRoomJoin($listener) : Wechaty {
        return $this->_on(EventEnum::ROOM_JOIN, $listener);
    }

    function onRoomLeave($listener) : Wechaty {
        return $this->_on(EventEnum::ROOM_LEAVE, $listener);
    }

    function onRoomTopic($listener) : Wechaty {
        return $this->_on(EventEnum::ROOM_TOPIC, $listener);
    }

    function onMessage($listener) : Wechaty {
        return $this->_on(EventEnum::MESSAGE, $listener);
    }

    function onFriendShip($listener) : Wechaty {
        return $this->_on(EventEnum::FRIENDSHIP, $listener);
    }

    function getPuppet() : Puppet {
        return $this->_puppet;
    }

    function friendship() : Friendship {
        return new Friendship($this);
    }

    function userSelf(): ContactSelf {
        $userId = $this->_puppet->selfId();
        $user = $this->contactManager->loadSelf($userId);
        return $user;
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
            $room->sync();

            $inviteeList = array();
            $inviteeListId = $payload["inviteeIdList"];//inviteeIdList
            foreach($inviteeListId as $value) {
                $contact = $this->contactManager->loadSelf($value);
                $contact->ready();
                $inviteeList[] = $contact;
            }

            $inviter = $this->contactManager->loadSelf($payload["inviterId"]);
            $inviter->ready();

            $date = new Date($payload["timestamp"]);
            $this->emit(EventEnum::ROOM_JOIN, $room, $inviteeList, $inviter, $date);
            $room->emit(EventEnum::JOIN, $inviteeList, $inviter, $date);
        });
        $puppet->on(EventEnum::ROOM_LEAVE, function($payload) {
            $room = $this->roomManager->load($payload["roomId"]);
            $room->sync();

            $leaverList = array();
            $removeeIdList = $payload["removeeIdList"];//removeeIdList
            foreach($removeeIdList as $value) {
                $contact = $this->contactManager->loadSelf($value);
                $contact->ready();
                $leaverList[] = $contact;
            }
            $remover = $this->contactManager->loadSelf($payload["removerId"]);
            $remover->ready();

            $date = new Date($payload["timestamp"]);

            $this->emit(EventEnum::ROOM_LEAVE, $room, $leaverList, $remover, $date);
            $room->emit(EventEnum::LEAVE, $leaverList, $remover, $date);
        });
        $puppet->on(EventEnum::ROOM_TOPIC, function($payload) {
            $room = $this->roomManager->load($payload["roomId"]);
            $room->sync();

            $changer = $this->contactManager->loadSelf($payload["changerId"]);
            $changer->ready();

            $date = new Date($payload["timestamp"]);

            $this->emit(EventEnum::ROOM_TOPIC, $room, $payload["newTopic"], $payload["oldTopic"], $changer, $date);
            $room->emit(EventEnum::TOPIC, $payload["newTopic"], $payload["oldTopic"], $changer, $date);
            $this->emit(EventEnum::ROOM_TOPIC, $payload);
        });
    }

    private function _initPuppetAccessory(PuppetHostie\PuppetHostie $puppet) {
        $this->contactManager = new ContactManager($this);
        $this->messageManager = new MessageManager($this);
        $this->roomManager = new RoomManager($this);
        $this->roomInvitationManager = new RoomInvitationManager($this);
        $this->imageManager = new ImageManager($this);
        $this->tagManager = new TagManager($this);
        $this->friendshipManager = new FriendshipManager($this);
    }
}