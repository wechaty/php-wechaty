<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:39 PM
 */
namespace IO\Github\Wechaty\PuppetHostie;

use IO\Github\Wechaty\Puppet\Puppet;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\PuppetHostie\Exceptions\PuppetHostieException;

class PuppetHostie extends Puppet {
    private $channel = null;
    private $grpcClient = null;

    const CHATIE_ENDPOINT = "https://api.chatie.io/v0/hosties/";

    private function discoverHostieIp() : array {
        $url = self::CHATIE_ENDPOINT . $this->puppetOptions->token;
        $client = new \GuzzleHttp\Client();
    }

    public static function get() {

    }
}