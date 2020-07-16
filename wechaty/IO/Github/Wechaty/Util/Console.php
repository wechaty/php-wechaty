<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 1:07 PM
 */
namespace IO\Github\Wechaty\Util;

class Console {
    public static function log($data) {
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
        echo $data . "\n";
    }
}