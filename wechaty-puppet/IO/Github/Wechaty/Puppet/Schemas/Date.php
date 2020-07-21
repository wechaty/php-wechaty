<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 8:13 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class Date {
    private $_timestamp;

    public function __construct($timestamp) {
        $this->_timestamp = $timestamp;
    }

    public function getTimestamp() {
        return $this->_timestamp;
    }

    public function getDate() {
        return date("Y-m-d H:i:s", $this->_timestamp);
    }
}