<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:50 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptionsBuffer {
    public $type;
    public $buffer;
    public $name;

    public function __construct($type, $buffer, $name) {
        $this->type = $type;
        $this->buffer = $buffer;
        $this->name = $name;
    }

    public function __toString() {
        return "FileBoxOptionsBuffer(type=$this->type, buffer=$this->buffer)";
    }
}