<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/24
 * Time: 10:40 AM
 */

use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\User\ContactSelf;
use IO\Github\Wechaty\User\MiniProgram;
use IO\Github\Wechaty\User\UrlLink;

define("ROOT", dirname(__DIR__));
// DEBUG should create dir use command sudo mkdir /var/log/wechaty && sudo chmod 777 /var/log/wechaty
define("DEBUG", 1);

function autoload($clazz) {
    $file = str_replace('\\', '/', $clazz);
    if(stripos($file, "PuppetHostie") > 0) {
        require ROOT . "/wechaty-puppet-service/$file.php";
    } elseif(stripos($file, "PuppetService") > 0) {
        require ROOT . "/wechaty-puppet-service/$file.php";
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

\IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter::reflection(\IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter::class);

print_r(\IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter::getProperties(\IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter::class));

$filter = new \IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter();
$filter->id = 1;
print_r($filter);
echo $filter . "\n";

$cache = \IO\Github\Wechaty\Puppet\Cache\CacheFactory::getCache();
$cache->set("aaa", "1");
$cache->set("aa1", "1");
$cache->set("bbb", "1");
$cache->set("ccc", "1");
print_r($cache->keys("aa"));

$array = array(
    array(
        "id" => 1,
        "topic" => "",
    ),
    array(
        "id" => 2,
        "topic" => "",
    ),
);
print_r(array_map(function($value) {
    return $value["id"];
}, $array));

$puppetOptions = new PuppetOptions();
$puppetOptions->token = "token";
$puppetOptions->endPoint = "endPoint";
print_r(json_encode($puppetOptions));