<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:50 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptionsStream extends FileBoxOptions {
    public $_type = FileBoxType::STREAM;
    public $stream;
    public $name;

    public function __construct($stream, $name) {
        $this->stream = $stream;
        $this->name = $name;
    }

    public function __toString() {
        return "FileBoxOptionsStream(type=$this->_type, buffer=$this->stream)";
    }
}