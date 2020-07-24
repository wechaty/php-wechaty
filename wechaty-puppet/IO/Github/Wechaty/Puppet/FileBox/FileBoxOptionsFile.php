<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:49 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;

class FileBoxOptionsFile extends FileBoxOptions {
    public $_type = FileBoxType::FILE;
    public $path;
    public $name;

    public function __construct($path, $name) {
        $this->path = $path;
        $this->name = $name;
    }

    public function __toString() {
        return "FileBoxOptionsFile(type=$this->_type, path='$this->path')";
    }
}