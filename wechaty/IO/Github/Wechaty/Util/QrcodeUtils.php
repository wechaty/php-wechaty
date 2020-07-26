<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 2:53 PM
 */
namespace IO\Github\Wechaty\Util;

use Coco\QRCode\QRCode;
use IO\Github\Wechaty\Exceptions\WechatyException;

class QrcodeUtils {
    const MAX_LEN = 7089;

    public static function getQr(String $text) : String {
        if(empty($text)) {
            return "empty text";
        }
        $QRCode = new QRCode(['level' => "L", 'size' => 6, 'margin' => 2]);
        $ret = $QRCode->encode($text)->toASCII();

        return $ret;
    }

    public static function guardQrCodeValue(String $value): String {
        if (strlen($value) > self::MAX_LEN) {
            throw new WechatyException("QR Code Value is larger then the max len. Did you return the image base64 text by mistake? See: https://github.com/wechaty/wechaty/issues/1889");
        }
        return $value;
    }
}