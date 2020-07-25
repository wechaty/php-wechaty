<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:11 PM
 */

use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;
use IO\Github\Wechaty\Puppet\Schemas\Query\FriendshipSearchCondition;
use IO\Github\Wechaty\User\Contact;
use IO\Github\Wechaty\User\ContactSelf;
use IO\Github\Wechaty\User\Friendship;
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

/*
 * https://wechaty.github.io/2019/07/18/send-miniprogram-using-padpro/
 * https://github.com/wechaty/wechaty/issues/1806
 * {
  "id": "1099177530",
  "timestamp": 1562210085,
  "type": 9,
  "filename": "1099177530-to-be-implement.txt",
  "fromId": "wxid_jy0q2q63aggm21",
  "roomId": "8397442379@chatroom",
  "text": "<?xml version=\"1.0\"?>\n<msg>\n\t<appmsg appid=\"\" sdkver=\"0\">\n\t\t<title>毛豆课堂-专注于少儿领域的在线教育平台</title>\n\t\t<des>毛豆课堂</des>\n\t\t<action />\n\t\t<type>33</type>\n\t\t<showtype>0</showtype>\n\t\t<soundtype>0</soundtype>\n\t\t<mediatagname />\n\t\t<messageext />\n\t\t<messageaction />\n\t\t<content />\n\t\t<contentattr>0</contentattr>\n\t\t<url>https://mp.weixin.qq.com/mp/waerrpage?appid=wxe638e634ed8b3907&amp;type=upgrade&amp;upgradetype=3#wechat_redirect</url>\n\t\t<lowurl />\n\t\t<dataurl />\n\t\t<lowdataurl />\n\t\t<appattach>\n\t\t\t<totallen>0</totallen>\n\t\t\t<attachid />\n\t\t\t<emoticonmd5 />\n\t\t\t<fileext />\n\t\t\t<cdnthumburl>30590201000452305002010002043591c1e102032f4f560204e87ac2dc02045d1d6f24042b777875706c6f61645f383339373434323337394063686174726f6f6d313439345f313536323231303038340204010400030201000400</cdnthumburl>\n\t\t\t<cdnthumbmd5>bc3adb4160b6cbbac4be374b2acae80d</cdnthumbmd5>\n\t\t\t<cdnthumblength>7453</cdnthumblength>\n\t\t\t<cdnthumbwidth>720</cdnthumbwidth>\n\t\t\t<cdnthumbheight>576</cdnthumbheight>\n\t\t\t<cdnthumbaeskey>f922a8fbce1e7c3a8627dc2a9678f455</cdnthumbaeskey>\n\t\t\t<aeskey>f922a8fbce1e7c3a8627dc2a9678f455</aeskey>\n\t\t\t<encryver>0</encryver>\n\t\t\t<filekey>8397442379@chatroom1494_1562210084</filekey>\n\t\t</appattach>\n\t\t<extinfo />\n\t\t<sourceusername>gh_a1cd71094745@app</sourceusername>\n\t\t<sourcedisplayname>毛豆课堂</sourcedisplayname>\n\t\t<thumburl />\n\t\t<md5 />\n\t\t<statextstr />\n\t\t<weappinfo>\n\t\t\t<username><![CDATA[gh_a1cd71094745@app]]></username>\n\t\t\t<appid><![CDATA[wxe638e634ed8b3907]]></appid>\n\t\t\t<type>2</type>\n\t\t\t<version>19</version>\n\t\t\t<weappiconurl><![CDATA[http://mmbiz.qpic.cn/mmbiz_png/5lFWgxHFPzeu01RyEibY7vb5iceGcpBu9mReAHiaiaoXF7BicEYNSM2HretSX7DUa9CmASvspmiaSPDhIWm4w7nibXlQw/640?wx_fmt=png&wxfrom=200]]></weappiconurl>\n\t\t\t<pagepath><![CDATA[pages/index/index.html?userId=5cff40a739b221001136af8a]]></pagepath>\n\t\t\t<shareId><![CDATA[0_wxe638e634ed8b3907_898744801_1562210083_0]]></shareId>\n\t\t\t<appservicetype>0</appservicetype>\n\t\t</weappinfo>\n\t</appmsg>\n\t<fromusername>wxid_jy0q2q63aggm21</fromusername>\n\t<scene>0</scene>\n\t<appinfo>\n\t\t<version>1</version>\n\t\t<appname></appname>\n\t</appinfo>\n\t<commenturl></commenturl>\n</msg>\n",
  "toId": "wxid_3kxyh21gj3gt22"

const xml = `<?xml version="1.0"?>\n<msg>\n\t<appmsg appid="" sdkver="0">\n\t\t<title>${content.title}</title>\n\t\t<des>${content.description}</des>\n\t\t<action>view</action>\n\t\t<type>${content.type}</type>\n\t\t<showtype>0</showtype>\n\t\t<soundtype>0</soundtype>\n\t\t<mediatagname />\n\t\t<messageext />\n\t\t<messageaction />\n\t\t<content />\n\t\t<contentattr>0</contentattr>\n\t\t<url>https://mp.weixin.qq.com/mp/waerrpage?appid=${content.appid}&amp;type=upgrade&amp;upgradetype=3#wechat_redirect</url>\n\t\t<lowurl />\n\t\t<dataurl />\n\t\t<lowdataurl />\n\t\t<songalbumurl />\n\t\t<songlyric />\n\t\t<appattach>\n\t\t\t<totallen>0</totallen>\n\t\t\t<attachid />\n\t\t\t<emoticonmd5></emoticonmd5>\n\t\t\t<fileext />\n\t\t\t<cdnthumburl>${content.cdnthumburl}</cdnthumburl>\n\t\t\t<cdnthumbmd5></cdnthumbmd5>\n\t\t\t<cdnthumblength>${content.cdnthumblength}</cdnthumblength>\n\t\t\t<cdnthumbwidth>${content.cdnthumbwidth}</cdnthumbwidth>\n\t\t\t<cdnthumbheight>${content.cdnthumbwidth}</cdnthumbheight>\n\t\t\t<cdnthumbaeskey>${content.cdnthumbaeskey}</cdnthumbaeskey>\n\t\t\t<aeskey>${content.aeskey}</aeskey>\n\t\t\t<encryver>0</encryver>\n\t\t\t<filekey>wxid_orp7dihe2pm112199_1587623589</filekey>\n\t\t</appattach>\n\t\t<extinfo />\n\t\t<sourceusername>${content.username}</sourceusername>\n\t\t<sourcedisplayname>${content.title}</sourcedisplayname>\n\t\t<thumburl />\n\t\t<md5 />\n\t\t<statextstr />\n\t\t<directshare>0</directshare>\n\t\t<weappinfo>\n\t\t\t<username><![CDATA[${content.username}]]></username>\n\t\t\t<appid><![CDATA[${content.appid}]]></appid>\n\t\t\t<type>0</type>\n\t\t\t<version>${content.version}</version>\n\t\t\t<weappiconurl><![CDATA[]]></weappiconurl>\n\t\t\t<pagepath><![CDATA[${content.pagepath}]]></pagepath>\n\t\t\t<appservicetype>0</appservicetype>\n\t\t\t<tradingguaranteeflag>0</tradingguaranteeflag>\n\t\t</weappinfo>\n\t</appmsg>\n\t<fromusername>${selfId}</fromusername>\n\t<scene>0</scene>\n\t<appinfo>\n\t\t<version>1</version>\n\t\t<appname />\n\t</appinfo>\n\t<commenturl />\n</msg>\n`
}*/
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
})->onMessage(function(\IO\Github\Wechaty\User\Message $message) use ($wechaty, $appId, $username) {
    $name = $message->from()->getPayload()->name;
    $text = $message->getPayload()->text;
    $type = $message->getPayload()->type;
    echo "message from user name $name\n";

    $room = $message->room();
    if($room) {
        if($text == "room") {
            $topic = $room->getPayload()->topic;
            echo "room topic:$topic";
            $room->say("hello $topic from PHP7.4");
        }
    }

    if($type == \IO\Github\Wechaty\Puppet\Schemas\MessagePayload::MESSAGETYPE_TEXT) {
        if($text == "ding") {
            $message->say("dong");
        } elseif($text == "群发") {
            $contactList = $wechaty->userSelf()->contactList();
            foreach($contactList as $value) {
                echo $value->name() . "\n";
            }
            foreach($contactList as $value) {
                //$value->say("hello {$value->name()}, the message is send to all friend by PHP7.4, Good Night.");
                sleep(2);
            }
        } elseif($text == "search") {
            $searchPhone = getenv("WECHAT_SEARCH_PHONE");

            $condition = new FriendshipSearchCondition();
            $condition->phone = $searchPhone;
            $contact = $wechaty->friendship()->search($condition);
            if($contact) {
                //search result:xxxx
                echo "search result:" . $contact->getPayload()->name . " {$contact->getPayload()->weixin}\n";
            } else {
                echo "search result empty\n";
            }
        } elseif($text = "add") {
            $wechatId = getenv("WECHAT_ADD_WECHAT_ID");

            $contact = $wechaty->contactManager->load($wechatId);
            $contact->ready();
            $wechaty->friendship()->add($contact, "hello from PHP7.4");
        } elseif($text == "hello") {
            $message->say("hello $name from PHP7.4");
            $url = "https://wx1.sinaimg.cn/mw690/46b94231ly1gh0xjf8rkhj21js0jf0xb.jpg";
            $fileBoxOptions = new \IO\Github\Wechaty\Puppet\FileBox\FileBoxOptionsUrl($url, "php-wechaty.png");
            $file = new FileBox($fileBoxOptions);
            $message->say($file);
            $url = "https://tb-m.luomor.com/";
            $urlLink = UrlLink::create($url);
            $message->say($urlLink);

            $payload = new MiniProgramPayload();
            $payload->appid = $appId;
            $payload->pagePath = "pages/index/index.html";
            $payload->title = "烙馍FM";
            $payload->description = "烙馍倾听";
            $payload->username = "$username@app"; // 'gh_xxxxxxx', get from mp.weixin.qq.com
            $payload->thumbUrl = "https://wx1.sinaimg.cn/mw690/46b94231ly1gh0xjf8rkhj21js0jf0xb.jpg";
            $payload->thumbKey = "";
            $miniProgram = new MiniProgram($payload);
            $message->say($miniProgram);
        } elseif(stripos($text, "@烙馍网") === 0) {
            $message->say("hello $name from PHP7.4");
        }
    } elseif($type == \IO\Github\Wechaty\Puppet\Schemas\MessagePayload::MESSAGETYPE_ATTACHMENT) {
        try {
            /**
             * {"mimeType":null,"base64":null,"remoteUrl":"http:\/\/siyouyunsy-1253559996.cos.ap-guangzhou.myqcloud.com\/","qrCode":null,"buffer":null,"localPath":null,"headers":[],"name":"","metadata":null,"boxType":2}
             */
            $fileBox = $message->toFileBox();
            echo $fileBox->toJsonString() . "\n";
        } catch(\IO\Github\Wechaty\Exceptions\WechatyException $e) {
            print_r($e);
        }
    } elseif($type == \IO\Github\Wechaty\Puppet\Schemas\MessagePayload::MESSAGETYPE_IMAGE) {
        /**
         * {"mimeType":null,"base64":null,"remoteUrl":"http:\/\/siyouyunsy-1253559996.cos.ap-guangzhou.myqcloud.com\/msg\/0FAPG\/20200724\/3182738554781097821_wxid_w3nebr749m5t21_1595589739165_.png?sign=q-sign-algorithm%3Dsha1%26q-ak%3DAKIDBi7d3I4UK7iDXkAhQyQsDMNGxY2KmlCY%26q-sign-time%3D1595589739%3B1681903339%26q-key-time%3D1595589739%3B1681903339%26q-header-list%3D%26q-url-param-list%3D%26q-signature%3D5fe1565b474c45d0500290a87e2000d75c1bb8c1","qrCode":null,"buffer":null,"localPath":null,"headers":[],"name":"3182738554781097821_wxid_w3nebr749m5t21_1595589739165_.png","metadata":null,"boxType":2}
         */
        $image = $message->toImage();
        echo $image->thumbnail()->toJsonString() . "\n";
    } elseif($type == \IO\Github\Wechaty\Puppet\Schemas\MessagePayload::MESSAGETYPE_MINIPROGRAM) {
        /**
         * {"appid":null,"description":"\u70d9\u998d\u503e\u542c","pagePath":"pages\/index\/index.html","thumbUrl":"https:\/\/wx1.sinaimg.cn\/mw690\/46b94231ly1gh0xjf8rkhj21js0jf0xb.jpg","title":"\u70d9\u998dFM","username":"gh_xxxxxxxxxx","thumbKey":null}
         */
        $miniProgram = $message->toMiniProgram();
        echo $miniProgram->getPayload()->toJsonString() . "\n";
    } elseif($type == \IO\Github\Wechaty\Puppet\Schemas\MessagePayload::MESSAGETYPE_URL) {
        /**
         * {"title":"\u70d9\u998d\u7701\u94b1-\u4f18\u60e0\u5238\u9886\u53d6","url":"https:\/\/tb-m.luomor.com\/","description":"\u70d9\u998d\u7701\u94b1\u5b98\u7f51-\u6bcf\u65e5\u66f4\u65b0\u5929\u732b\u6dd8\u5b9d\u4f18\u60e0\u5238\uff0c\u66f4\u67099.9\u5305\u90ae\u4e13\u533a\uff0c\u54c1\u724c\u4f18\u9009\uff0c\u627e\u5238\u529f\u80fd\u9f50\u5168\u3002\u805a\u5212\u7b97\u7684\u4f18\u60e0\u5238\uff0c\u6dd8\u5b9e\u60e0\u7684\u8d2d\u7269\u7f51\u7ad9\uff0c\u5927\u54c1\u724c\u5546\u54c1\u53ef\u4eab\u6298\u4e0a\u6298\uff0c\u5404\u79cd\u8d85\u503c\u5546\u54c1\u53ea\u9700\u4e00\u952e\u641c\u7d22\u3002\u7279\u5356\u3001\u4f4e\u4ef7\u3001\u767d\u83dc\u4ef7\uff0c\u5c3d\u5728\u70d9\u998d\u7701\u94b1","thumbnailUrl":"https:\/\/img.alicdn.com\/imgextra\/i4\/790237325\/O1CN01hY4aU523ytm2F4HxA_!!790237325.jpg?t=1586059949000"}
         */
        $urlLink = $message->toUrlLink();
        echo $urlLink->getPayload()->toJsonString() . "\n";
    }

})->onFriendShip(function(Friendship $friendship) {
    if($friendship->getPayload()->type == \IO\Github\Wechaty\Puppet\Schemas\FriendshipPayload::FRIENDSHIPTYPE_RECEIVE) {
        $friendship->accept();
    }
})->onHeartBeat(function($data) use ($wechaty) {
    // {"data":"heartbeat@browserbridge ding","timeout":60000}
    echo $data . "\n";
    // $wechaty->stop();
})->start();