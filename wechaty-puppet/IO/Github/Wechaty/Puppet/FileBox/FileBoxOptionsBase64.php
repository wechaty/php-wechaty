<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:50 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptionsBase64 extends FileBoxOptions {
    public $type = FileBoxType::BASE64;
    public $base64;
    public $name;

    public function __construct($base64, $name) {
        $this->base64 = $base64;
        $this->name = $name;
    }

    public function __toString() {
        return "FileBoxOptionsBase64(type=$this->type, buffer=$this->base64)";
    }
}