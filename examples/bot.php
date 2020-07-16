<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:11 PM
 */
define("ROOT", dirname(__DIR__));
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

$token = getenv("WECHATY_PUPPET_HOSTIE_TOKEN");
$wechaty = \IO\Github\Wechaty\Wechaty::getInstance($token);
$wechaty->start();

\IO\Github\Wechaty\Util\Logger::warn();
\IO\Github\Wechaty\Puppet\Util\JsonUtil::get();
\IO\Github\Wechaty\Puppet\StateEnum::PENDING;
\IO\Github\Wechaty\PuppetHostie\Util\FutureUtil::get();
\IO\Github\Wechaty\PuppetHostie\GrpcPuppet::get();
\IO\Github\Wechaty\PuppetMock\MockData::get();
\IO\Github\Wechaty\PuppetMock\Util\MockitoHelper::get();