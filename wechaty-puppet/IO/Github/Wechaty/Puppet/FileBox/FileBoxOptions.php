<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:28 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptions {
    public $type;
    public $name;

    public function __construct($type, $name) {
        $this->type = $type;
        $this->name = $name;
    }
}