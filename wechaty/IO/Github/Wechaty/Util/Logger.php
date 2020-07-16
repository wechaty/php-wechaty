<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:35 PM
 */
namespace IO\Github\Wechaty\Util;

class Logger {
    // sudo mkdir /var/log/wechaty && sudo chmod 777 /var/log/wechaty
    const LOGERPREFIX = '/var/log/wechaty/';

    private static $_logs = [];
    private static $logLevel = [
        LOG_EMERG => 'EMERG',
        LOG_CRIT => 'CRIT',
        LOG_ERR => 'ERROR',
        LOG_WARNING => 'WARNING',
        LOG_NOTICE => 'NOTICE',
        LOG_INFO => 'INFO',
        LOG_DEBUG => 'DEBUG',
    ];

    public static function GetLogs() {
        $log = self::$_logs;
        $log[] = ["hostname:" . gethostname()];
        self::$_logs = [];
        return $log;
    }

    public static function clearLogs() {
        self::$_logs = [];
    }

    public static function EMERG($logData = array(), $logData2 = array()) {
        self::_log(LOG_EMERG, func_get_args());
    }

    public static function CRIT($logData = array(), $logData2 = array()) {
        self::_log(LOG_CRIT, func_get_args());
    }

    public static function ERR($logData = array(), $logData2 = array()) {
        self::_log(LOG_ERR, func_get_args());
    }

    public static function WARNING($logData = array(), $logData2 = array()) {
        self::_log(LOG_WARNING, func_get_args());
    }

    public static function NOTICE($logData = array(), $logData2 = array()) {
        self::_log(LOG_NOTICE, func_get_args());
    }

    public static function INFO($logData = array(), $logData2 = array()) {
        self::_log(LOG_INFO, func_get_args());
    }

    public static function DEBUG($logData = array(), $logData2 = array()) {
        self::_log(LOG_DEBUG, func_get_args());
    }

    private static function _log($logType = LOG_INFO, $logArgs = array()) {
        array_shift($logArgs);
        //未定义debug模式时，当log的级别大于信息6（LOG_DEBUG => 'DEBUG', 7）
        if (!(defined('DEBUG') && DEBUG > 0) && $logType > LOG_INFO) {
            return;
        }

        $logLevel = isset(self::$logLevel[$logType]) ? self::$logLevel[$logType] : '';
        $logs = ['time' => date("Y-m-d H:i:s"), 'LEVEL' => $logLevel];

        foreach ($logArgs as $logData) {
            foreach ((array) $logData as $key => $value) {
                if ($key === 'DEBUG') {
                    $value = '<<<<' . join("\t", (array) $value) . '>>>>';
                } else {
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                    }
                }
                $value = str_replace("/\b+/", ' ', $value);
                if (is_int($key)) {
                    $logs[] = $value;
                } else {
                    $logs[] = $key . ':' . $value;
                }
            }
        }
        $logStr = join("]\t[", $logs) . "\n";
        self::_cacheLog($logLevel, $logArgs);
        self::_writeLog($logLevel, $logStr);
        if ($logType <= LOG_ERR) {
            error_log($logStr);
        }
    }

    private static function _getFilePath() {
        return self::LOGERPREFIX . "wechaty_log-" . date("Ymd");
    }

    private static function _writeLog($logLevel, $logStr) {
        $logFile = self::_getFilePath();
        if(!file_exists($logFile)) {
            self::_mkfile($logFile);
        }
        if(PHP_SAPI == 'cli') {
            file_put_contents($logFile, $logStr, FILE_APPEND);
        } else {
            file_put_contents($logFile, $logStr, FILE_APPEND);
        }
    }

    private static function _cacheLog($logLevel, $log) {
        if (!defined('DEBUG') || DEBUG <= 0) {
            return;
        }
        if ('cli' == PHP_SAPI) {
            print_r($log);
            return;
        }
        if (count(self::$_logs) < 40) {
            self::$_logs[] = [$logLevel, $log];
        }
    }

    private static function _mkfile($logFile) {
        if (!file_exists($logFile)) {
            $logFolder = dirname($logFile);
            if (!is_dir($logFolder)) {
                if (!self::Mkdirs($logFolder)) {
                    return false;
                }
            }
            touch($logFile);
            chmod($logFile, 0777);
        }
        return true;
    }

    private static function Mkdirs($dir) {
        if (!is_dir($dir)) {
            if (!self::Mkdirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, 0777)) {
                return false;
            } else {
                chmod($dir, 0777);
            }
        }
        return true;
    }
}