<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 4:15 PM
 */
namespace IO\Github\Wechaty\Dom;

class DomFactory {
    const DEFAULT_DOM = "DiDom";
    const CACHE_DIDOM = "DiDom";

    /**
     * @param string $type
     * @return Dom | DiDom
     */
    public static function getDom($type = self::CACHE_DIDOM) {
        if($type == self::CACHE_DIDOM) {
            return DiDom::getInstance();
        }

        return Dom::getInstance();
    }
}