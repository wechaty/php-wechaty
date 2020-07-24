<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/24
 * Time: 9:18 PM
 */
namespace IO\Github\Wechaty\Puppet\Util;

class Filter {
    public static function int($str, $min = null, $max = null) {
        $options = array(
            'options' => array('min_range' => $min, 'max_range' => $max),
            'flags' => FILTER_FLAG_ALLOW_OCTAL | FILTER_FLAG_ALLOW_HEX,
        );
        return filter_var($str, FILTER_VALIDATE_INT, $options);
    }

    public static function float($str) {
        $options = array(
            'flags' => FILTER_FLAG_ALLOW_THOUSAND
        );
        return filter_var($str, FILTER_VALIDATE_FLOAT, $options);
    }

    public static function ip($str) {
        return filter_var($str, FILTER_VALIDATE_IP);
    }

    public static function email($str, $allowDomains = null) {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public static function boolean($str) {
        return filter_var($str, FILTER_VALIDATE_BOOLEAN);
    }

    public static function regexp($str, $regexp) {
        $options = array(
            'options' => array('regexp' => $regexp)
        );
        return filter_var($str, FILTER_VALIDATE_REGEXP, $options);
    }

    public static function str($str) {
        return $str;
    }

    public static function mobile($str) {
        if (empty($str)) {
            return false;
        }
        if ($str[0] !== '+') {
            return self::regexp($str, "/^1[3456789]\d{9}$/");
        } else {
            return self::regexp($str, "/^(\+\d{1,7}\-)?1[3456789]\d{9}$/");
        }
    }
}