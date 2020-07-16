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
use LM\Exception;

class Wechaty extends EventEmitter {

    private $puppetOptions = null;
    private $wechatyOptions = null;

    private $status = StateEnum::OFF;

    /**
     * Wechaty constructor.
     * @param $wechatyOptions \IO\Github\Wechaty\Puppet\Schemas\WechatyOptions
     */
    public function __construct($wechatyOptions) {
        $this->wechatyOptions = $wechatyOptions;
        $this->puppetOptions = $wechatyOptions->puppetOptions;
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
        //puppet.start().get();
        $status = StateEnum::ON;
        $this->emit(EventEnum::START, "");

        //addHook();
        echo "start Wechaty";
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
                print_r($ret->key());//0 1 2
                $response = $ret->current();
                print_r($response->getType());//2
                print_r($response->getPayload());
                //{"qrcode":"https://login.weixin.qq.com/l/IaysbZa04Q==","status":5}
                //{"data":"heartbeat@browserbridge ding","timeout":60000}
                //$client->DingSimple($dingRequest);
                //3{"data":"dong"}
                $ret->next();
            }
            print_r($ret->getReturn());
        } catch (\Exception $e) {
            echo " service stopped, interrupted by other thread!";
            print_r($e);
        }
        return $this;
    }

    private function _initPuppet() {

    }
}