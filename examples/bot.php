<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:11 PM
 */

use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;
use IO\Github\Wechaty\User\ContactSelf;
use IO\Github\Wechaty\User\MiniProgram;
use IO\Github\Wechaty\User\UrlLink;

define("ROOT", dirname(__DIR__));
// DEBUG should create dir use command sudo mkdir /var/log/wechaty && sudo chmod 777 /var/log/wechaty
define("DEBUG", 1);

function autoload($clazz) {
    $file = str_replace('\\', '/', $clazz);
    if(stripos($file, "PuppetHostie") > 0) {
        require ROOT . "/wechaty-puppet-hostie/$file.php";
    } elseif(stripos($file, "PuppetMock") > 0) {
        require ROOT . "/wechaty-puppet-mock/$file.php";
    } elseif(stripos($file, "Puppet") > 0) {
        require ROOT . "/wechaty-puppet/$file.php";
    } else {
        if(is_file(ROOT . "/wechaty/$file.php")) {
            require ROOT . "/wechaty/$file.php";
        }
    }
}

spl_autoload_register("autoload");

require ROOT . '/vendor/autoload.php';

// change dir
// \IO\Github\Wechaty\Util\Logger::$_LOGGER_DIR = "/tmp/";

$token = getenv("WECHATY_PUPPET_HOSTIE_TOKEN");
$endPoint = getenv("WECHATY_PUPPET_HOSTIE_ENDPOINT");
$appId = getenv("WECHAT_MINI_PROGRAM_APPID");
$username = getenv("WECHAT_MINI_PROGRAM_USERNAME");
$wechaty = \IO\Github\Wechaty\Wechaty::getInstance($token, $endPoint);
$wechaty->onScan(function($qrcode, $status, $data) {
    //{"qrcode":"http://weixin.qq.com/x/IcPycVXZP4RV8WZ9MXF-","status":2}
    //[0] => PuppetHostie 22 payload {"qrcode":"","status":3}
    if($status == 3) {
        echo "SCAN_STATUS_CONFIRMED\n";
    } else {
        $qr = \IO\Github\Wechaty\Util\QrcodeUtils::getQr($qrcode);
        echo "$qr\n\nOnline Image: https://wechaty.github.io/qrcode/$qrcode\n";
    }
})->onLogin(function(ContactSelf $user) {
    echo "login user id " . $user->getId() . "\n";
    echo "login user name " . $user->getPayload()->name . "\n";
})->onMessage(function(\IO\Github\Wechaty\User\Message $message) use ($appId, $username) {
    $name = $message->from()->getPayload()->name;
    $text = $message->getPayload()->text;
    echo "message from user name $name\n";
    if($text == "ding") {
        $message->say("dong");
    } else if($text == "hello") {
        $message->say("hello $name from PHP7.4");
        $url = "https://wx1.sinaimg.cn/mw690/46b94231ly1gh0xjf8rkhj21js0jf0xb.jpg";
        $fileBoxOptions = new \IO\Github\Wechaty\Puppet\FileBox\FileBoxOptionsUrl($url, "php-wechaty.png");
        $file = new FileBox($fileBoxOptions);
        $message->say($file);
        $url = "https://tb-m.luomor.com/";
        $urlLink = UrlLink::create($url);
        $message->say($urlLink);

        $payload = new MiniProgramPayload();
        //$payload->appId = $appId;
        //$payload->pagePath = "pages/index/index";
        //$payload->title = "烙馍FM";
        //$payload->description = "烙馍倾听";
        $payload->username = $username; // 'gh_xxxxxxx', get from mp.weixin.qq.com
        //$payload->thumbUrl = "https://wx1.sinaimg.cn/mw690/46b94231ly1gh0xjf8rkhj21js0jf0xb.jpg";
        //$payload->thumbKey = "key";
        $miniProgram = new MiniProgram($payload);
        $message->say($miniProgram);
    } else if(stripos($text, "@烙馍网") === 0) {
        $message->say("hello $name from PHP7.4");
    }
})->onHeartBeat(function($data) use ($wechaty) {
    // {"data":"heartbeat@browserbridge ding","timeout":60000}
    echo $data . "\n";
    // $wechaty->stop();
})->start();