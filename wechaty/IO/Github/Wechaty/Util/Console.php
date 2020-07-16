<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 1:07 PM
 */
namespace IO\Github\Wechaty\Util;

class Console {
    public static function logs(...$data) {
        $log = func_get_args();
        $logStr = "";
        foreach($log as $value) {
            $logStr .= self::getStr($value) . " ";
        }
        echo $logStr . "\n";
    }

    public static function log($data) {
        $data = self::getStr($data);
        echo $data . "\n";
    }

    public static function logStr($data, $ln = false) {
        if(!is_string($data)) {
            return;
        }
        echo $data;
        if($ln) {
            echo "\n";
        }
    }

    private static function getStr($data) {
        /*
         * "boolean"
         * "integer"
         * "double" (for historical reasons "double" is
         * returned in case of a float, and not simply
         * "float")
         * "string"
         * "array"
         * "object"
         * "resource"
         * "NULL"
         * "unknown type"
         * "resource (closed)" since 7.2.0*/
        if(gettype($data) == "array") {
            $data = json_encode($data);
        } elseif(gettype($data) == "object") {
            $data = json_encode($data);
        }
        return $data;
    }
}