<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:50 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptionsStream {
    public $type;
    public $stream;
    public $name;

    public function __construct($type, $stream, $name) {
        $this->type = $type;
        $this->stream = $stream;
        $this->name = $name;
    }

    public function __toString() {
        return "FileBoxOptionsStream(type=$this->type, buffer=$this->stream)";
    }
}