<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 4:15 PM
 */
namespace IO\Github\Wechaty\Dom;

class Dom {
    protected $_dom_parser;

    public static function getInstance() {
        static $_instance = NULL;
        if (empty($_instance)) {
            $_instance = new static();
        }
        return $_instance;
    }

    public function getShareInfo($url) {

    }
}