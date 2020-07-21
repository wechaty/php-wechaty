<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 2:53 PM
 */
namespace IO\Github\Wechaty\Util;

use Coco\QRCode\QRCode;

class QrcodeUtils {
    public static function getQr(String $text) : String {
        if(empty($text)) {
            return "empty text";
        }
        $QRCode = new QRCode(['level' => "L", 'size' => 6, 'margin' => 2]);
        $ret = $QRCode->encode($text)->toASCII();

        return $ret;
    }
}