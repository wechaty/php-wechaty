<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:23 PM
 */
namespace IO\Github\Wechaty;

use IO\Github\Wechaty\Puppet\EventEmitter\EventEmitter;
use IO\Github\Wechaty\Puppet\Schemas\Event\EventScanPayload;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\Schemas\WechatyOptions;
use IO\Github\Wechaty\Puppet\StateEnum;
use IO\Github\Wechaty\Util\Console;
use IO\Github\Wechaty\Util\Logger;
use LM\Exception;

class Wechaty extends EventEmitter {

    private $_puppetOptions = null;
    private $_wechatyOptions = null;

    private $_status = StateEnum::OFF;

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

    private function _initPuppet() {
        if($this->_puppet) {
            return;
        }

        $this->_puppet = new PuppetHostie\PuppetHostie($this->_puppetOptions);

        $this->_initPuppetEventBridge($this->_puppet);
    }

    function onScan($listener) : Wechaty {
        return $this->_on(EventEnum::SCAN, $listener);
    }

    function onHeartBeat($listener) : Wechaty {
        return $this->_on(EventEnum::HEART_BEAT, $listener);
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
            $this->emit(EventEnum::HEART_BEAT, $payload);
        });
        $puppet->on(EventEnum::DONG, function($payload) {
            $this->emit(EventEnum::DONG, $payload);
        });
        $puppet->on(EventEnum::ERROR, function($payload) {
            $this->emit(EventEnum::ERROR, $payload);
        });
        $puppet->on(EventEnum::FRIENDSHIP, function($payload) {
            $this->emit(EventEnum::FRIENDSHIP, $payload);
        });
        $puppet->on(EventEnum::LOGIN, function($payload) {
            $this->emit(EventEnum::LOGIN, $payload);
        });
        $puppet->on(EventEnum::LOGOUT, function($payload) {
            $this->emit(EventEnum::LOGOUT, $payload);
        });
        $puppet->on(EventEnum::MESSAGE, function($payload) {
            $this->emit(EventEnum::MESSAGE, $payload);
        });
        $puppet->on(EventEnum::READY, function($payload) {
            $this->emit(EventEnum::READY, $payload);
        });
        $puppet->on(EventEnum::ROOM_INVITE, function($payload) {
            $this->emit(EventEnum::ROOM_INVITE, $payload);
        });
        $puppet->on(EventEnum::ROOM_JOIN, function($payload) {
            $this->emit(EventEnum::ROOM_JOIN, $payload);
        });
        $puppet->on(EventEnum::ROOM_LEAVE, function($payload) {
            $this->emit(EventEnum::ROOM_LEAVE, $payload);
        });
        $puppet->on(EventEnum::ROOM_TOPIC, function($payload) {
            $this->emit(EventEnum::ROOM_TOPIC, $payload);
        });
    }
}