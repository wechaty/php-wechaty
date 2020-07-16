<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:23 PM
 */
namespace IO\Github\Wechaty;

use IO\Github\Wechaty\Puppet\EventEmitter\EventEmitter;
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
    private $_puppet = null;

    /**
     * Wechaty constructor.
     * @param $wechatyOptions \IO\Github\Wechaty\Puppet\Schemas\WechatyOptions
     */
    public function __construct($wechatyOptions) {
        $this->_wechatyOptions = $wechatyOptions;
        $this->_puppetOptions = $wechatyOptions->puppetOptions;
    }

    public static function getInstance($token) {
        $puppetOptions = new PuppetOptions();
        $puppetOptions->token = $token;
        $wechatyOptions = new WechatyOptions();
        $wechatyOptions->puppetOptions = $puppetOptions;
        return new Wechaty($wechatyOptions);
    }

    public function start() : Wechaty {
        $this->_initPuppet();
        //$this->_puppet->start()->get();
        $status = StateEnum::ON;
        $this->emit(EventEnum::START, "");

        //addHook();
        echo "start Wechaty\n";
        try {
            $client = new \Wechaty\PuppetClient("localhost:8788", [
                'credentials' => \Grpc\ChannelCredentials::createInsecure()
            ]);
            $startRequest = new \Wechaty\Puppet\StartRequest();
            $client->Start($startRequest);

            $eventRequest = new \Wechaty\Puppet\EventRequest();
            $call = $client->Event($eventRequest);
            $ret = $call->responses();//Generator Object
            while($ret->valid()) {
                Console::logStr($ret->key() . " ");//0 1 2
                $response = $ret->current();
                Console::logStr($response->getType() . " ");//2
                Console::logStr($response->getPayload() . " ");
                //{"qrcode":"https://login.weixin.qq.com/l/IaysbZa04Q==","status":5}
                //{"data":"heartbeat@browserbridge ding","timeout":60000}
                //$client->DingSimple($dingRequest);
                //3{"data":"dong"}
                echo "\n";
                $ret->next();
            }
            echo "service stopped normally\n";
            Console::log($ret->getReturn());
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
    }
}