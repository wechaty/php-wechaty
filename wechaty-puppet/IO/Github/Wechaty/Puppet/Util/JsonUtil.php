<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:42 PM
 */
namespace IO\Github\Wechaty\Puppet\Util;

class JsonUtil {
    public static function readValue(String $json) {
        return json_decode($json, true);
    }

    public static function write($input) : String {
        return json_encode($input);
    }
}