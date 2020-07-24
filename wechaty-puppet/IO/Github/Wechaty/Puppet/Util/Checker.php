<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/24
 * Time: 9:18 PM
 */
namespace IO\Github\Wechaty\Puppet\Util;

class Checker {

    public static function InvalidArgumentException($field, $value, $error) {
        if (is_array($value)) {
            $value = 'ARRAY';
        }
        throw new \InvalidArgumentException("$field: {$field}[$value] $error", 400);
    }

    public static function notNULL($str, $text = null) {
        $ret = !is_null($str);
        if ($ret || $text === null) {
            return $ret;
        }
        self::InvalidArgumentException($text, $str, "is NULL");
    }

    /**
     * 校验密码复杂度
     *
     * @param string $str
     * @param int    $type
     * @param string $text
     *
     * @return string
     */
    public static function password($str, $passwordType, $text = null) {
        if ($passwordType != 1) {
            return true;
        } else {
            // 用户填写的复杂度大于等于2
            $complexity = self::_checkPasswordComplexity($str);
            if ($complexity >= 2 || $text === null) {
                return $complexity;
            }
        }
        $len = strlen($str);
        if ($len >= 6 || $text === null) {
            return $str;
        }
        self::InvalidArgumentException($text, str_pad('', strlen($str), '*'), "is not under  passwordType");
    }

    public static function number($value, $min = null, $max = null, $field = null) {
        $ret = is_numeric($value);
        if ($min !== null) {
            $ret = $ret && $value >= $min;
        }
        if ($max !== null) {
            $ret = $ret && $value <= $max;
        }
        if ($ret || $field === null) {
            return $ret;
        }
        self::InvalidArgumentException($field, $value, "is not between $min~$max");
    }

    public static function inArray($needle, $haystack, $field = NULL) {
        $ret = !in_array($needle, $haystack) ? false : $needle;
        if ($ret!==FALSE || $field === null) {
            return $ret;
        }
        self::InvalidArgumentException($field, $needle, ", it's not in [" . join(',', $haystack) . ']');
    }

    public static function numbers($values, $min = null, $max = null, $field = null) {
        foreach ($values as $value) {
            self::number($value, $min, $max, $field);
        }
    }

    public static function mobile($value, $fields = null) {
        $ret = Filter::mobile($value);
        if ($ret || $fields === null) {
            return $ret;
        }
        self::InvalidArgumentException($fields, $value, "is not mobile");
    }

    public static function email($value, $field = null) {
        $ret = Filter::email($value);
        if ($ret || $field === null) {
            return $ret;
        }
        self::InvalidArgumentException($field, $value, "is not email");
    }

    public static function error($field, $error = 'is error', $value = '') {
        self::InvalidArgumentException($field, $value, $error);
    }

    public static function notempty($value, $field = null, $msg = "is empty") {
        $ret = !empty($value);
        if ($ret || $field === null) {
            return $ret;
        }
        self::InvalidArgumentException($field, $value, $msg);
    }

    public static function notEmptyArray($arr, $field = null) {
        $ret = is_array($arr) && count($arr) != 0;
        if ($ret || $field === null) {
            return $ret;
        }
        self::InvalidArgumentException($field, $arr, "is empty array");
    }

    public static function mustFields($data, $mustFields) {
        foreach ($mustFields as $field) {
            if(!isset($data[$field])) {
                self::InvalidArgumentException($field, "", "is not set");
            }
        }
        return true;
    }

    public static function mustNotEmptyFields($data, $mustFields) {
        foreach ($mustFields as $field) {
            self::notempty(isset($data[$field]) ? $data[$field] : NULL, $field);
        }
    }

    public static function readOnlyFields($data, $readOnlyFields) {
        foreach ($readOnlyFields as $field) {
            if (isset($data[$field])) {
                self::InvalidArgumentException($field, $data[$field], 'is readOnly');
            }
        }
    }

    public static function numberFields($data, $numberFields, $min = NULL, $max = NULL) {
        foreach ($numberFields as $field) {
            if (isset($data[$field])) {
                self::number($data[$field], $min, $max, $field);
            }
        }
    }

    /**
     * 计算密码复杂度
     *
     */
    protected static function _checkPasswordComplexity($password) {
        $len = strlen($password);
        if ($len < 8) {
            return false;
        }

        $result = [
            'upper' => 0,
            'lower' => 0,
            'number' => 0,
            'other' => 0,
        ];
        for ($i = 0; $i < $len; $i++) {
            $number = ord($password[$i]);
            if ($number >= 65 && $number <= 90) {
                $result['upper'] ++;
            } else if ($number >= 97 && $number <= 122) {
                $result['lower'] ++;
            } else if ($number >= 48 && $number <= 57) {
                $result['number'] ++;
            } else {
                $result['other'] ++;
            }
        }
        return count(array_filter($result));
    }

    public static function ip($ip, $text = null) {
        $ret = Filter::ip($ip);
        if ($ret || $text === null) {
            return $ret;
        }
        self::InvalidArgumentException($text, $ip, "is not ip");
    }

    public static function length($str, $min, $max, $text = null, $msg = "is not between") {
        $ret = true;
        $len = mb_strlen($str, 'UTF-8');
        if ($min !== null) {
            $ret = $ret && $len >= $min;
        }
        if ($max !== null) {
            $ret = $ret && $len <= $max;
        }
        if ($ret || $text === null) {
            return $ret;
        }
        self::InvalidArgumentException($text, $str, " $msg [$min]~[$max] ");
    }

    public static function passwordLength($str, $min, $max, $text = null, $msg = "is not between") {
        $ret = true;
        $len = mb_strlen($str, 'UTF-8');
        if ($min !== null) {
            $ret = $ret && $len >= $min;
        }
        if ($max !== null) {
            $ret = $ret && $len <= $max;
        }
        if ($ret || $text === null) {
            return $ret;
        }
        self::InvalidArgumentException($text, "", " $msg [$min]~[$max] ");
    }

    public static function equal($actur, $expect, $text = NULL) {
        $ret = ( $actur == $expect ? $actur : NULL);
        if ($ret || $text === null) {
            return $ret;
        }

        self::InvalidArgumentException($text, $actur, "expect[$expect] ");
    }

    public static function string($str, $minLenght, $maxLenth, $text = null) {
        $len = strlen($str);
        if ($len >= $minLenght && $len <= $maxLenth) {
            return $str;
        } else if ($text === null) {
            return FALSE;
        }
        self::InvalidArgumentException($text, $str, "which length isnot between $minLenght~$maxLenth");
    }

    public static function thanHundred($amount, $minAmount = 10000, $text = null) {
        $ret = $amount >= $minAmount ? true : false;
        if ($ret || $text === null) {
            return $ret;
        }
        self::InvalidArgumentException($text, $amount / 100, "is not an integer greater than or equal to 100");
    }

}
