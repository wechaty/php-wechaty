<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:39 PM
 */
namespace IO\Github\Wechaty\PuppetHostie;

use IO\Github\Wechaty\Puppet\Puppet;
use IO\Github\Wechaty\Puppet\Schemas\Event\EventScanPayload;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\StateEnum;
use IO\Github\Wechaty\PuppetHostie\Exceptions\PuppetHostieException;
use IO\Github\Wechaty\Util\Console;
use IO\Github\Wechaty\Util\Logger;
use Wechaty\Puppet\EventResponse;

class PuppetHostie extends Puppet {
    private $_channel = null;
    /**
     * @var null|\Wechaty\PuppetClient
     */
    private $_grpcClient = null;

    const CHATIE_ENDPOINT = "https://api.chatie.io/v0/hosties/";

    public static function get() {

    }

    public function start() {
        if(self::$_STATE == StateEnum::ON) {
            Logger::WARNING("start() is called on a ON puppet. await ready(on) and return.");
            self::$_STATE = StateEnum::ON;
            return true;
        }
        self::$_STATE = StateEnum::PENDING;

        try {
            $this->_startGrpcClient();
            $this->_startGrpcStream();
            self::$_STATE = StateEnum::ON;
        } catch (\Exception $e) {
            Logger::ERR("start() rejection:", $e);
            self::$_STATE = StateEnum::OFF;
        }

        return true;
    }

    public function stop() {

    }

    private function _startGrpcClient() {
        $endPoint = $this->_puppetOptions ? $this->_puppetOptions->endPoint : "";
        $discoverHostieIp = array();
        if(empty($endPoint)) {
            $discoverHostieIp = $this->_discoverHostieIp();
        } else {
            $split = explode(":", $endPoint);
            if (sizeof($split) == 1) {
                $discoverHostieIp[0] = $split[0];
                $discoverHostieIp[1] = "8788";
            } else {
                $discoverHostieIp = $split;
            }
        }

        if (empty($discoverHostieIp[0]) || $discoverHostieIp[0] == "0.0.0.0") {
            Logger::ERR("cannot get ip by token, check token");
            exit;
        }
        $hostname = $discoverHostieIp[0] . ":" . $discoverHostieIp[1];

        $this->_grpcClient = new \Wechaty\PuppetClient($hostname, [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
        return $this->_grpcClient;
    }

    private function _startGrpcStream() {
        $startRequest = new \Wechaty\Puppet\StartRequest();
        $this->_grpcClient->Start($startRequest);

        $eventRequest = new \Wechaty\Puppet\EventRequest();
        $call = $this->_grpcClient->Event($eventRequest);
        $ret = $call->responses();//Generator Object
        while($ret->valid()) {
            Console::logStr($ret->key() . " ");//0 1 2
            $response = $ret->current();
            $this->_onGrpcStreamEvent($response);
            $ret->next();
        }
        echo "service stopped normally\n";
        Console::log($ret->getReturn());
    }

    private function _discoverHostieIp() : array {
        $url = self::CHATIE_ENDPOINT . $this->_puppetOptions->token;
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $url);

        $ret = array();
        if($response->getStatusCode() == 200) {
            Logger::DEBUG("$url with response " . $response->getBody());
            $ret = json_decode($response->getBody(), true);
            if(json_last_error()) {
                Logger::ERR("_discoverHostieIp json_decode with error " . json_last_error_msg());
                throw new PuppetHostieException("_discoverHostieIp json_decode with error " . json_last_error_msg());
            }
            return array($ret["ip"], $ret["port"]);
        } else {
            Logger::ERR("_discoverHostieIp request error with not 200, code is " . $response->getStatusCode());
        }

        return $ret;
    }

    private function _onGrpcStreamEvent(EventResponse $event) {
        try {
            $type = $event->getType();
            $payload = $event->getPayload();

            Logger::DEBUG("PuppetHostie $type payload $payload");

            switch ($type) {
                case EventEnum::SCAN:
                    $eventScanPayload = json_decode($payload, EventScanPayload::class);
                    Logger::DEBUG("scan pay load is {}", $eventScanPayload);
                    $this->emit(EventEnum::SCAN, $eventScanPayload);
                    break;
                default:
                    Console::logStr($event->getType() . " ");//2
                    Console::logStr($event->getPayload() . " ");
                    //{"qrcode":"https://login.weixin.qq.com/l/IaysbZa04Q==","status":5}
                    //{"data":"heartbeat@browserbridge ding","timeout":60000}
                    //$client->DingSimple($dingRequest);
                    //3{"data":"dong"}
                    echo "\n";
            }
        } catch (\Exception $e) {
            Logger::ERR("_onGrpcStreamEvent error", $e);
        }
    }
}