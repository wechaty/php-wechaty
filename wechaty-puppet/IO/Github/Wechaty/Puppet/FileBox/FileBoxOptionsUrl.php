<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:29 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptionsUrl extends FileBoxOptions {
    public $_type = FileBoxType::URL;
    public $url;
    public $name;
    public array $headers = array();

    public function __construct($url, $name) {
        $this->url = $url;
        $this->name = $name;
    }

    public function __toString() {
        return "FileBoxOptionsUrl(type=$this->_type, path='$this->url', headrs=" . json_encode($this->headers) . "";
    }
}