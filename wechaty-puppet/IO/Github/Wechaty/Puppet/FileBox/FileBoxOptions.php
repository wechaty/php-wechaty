<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:28 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxOptions {
    public $_type;
    public $name;

    public function getType() {
        return $this->_type;
    }
}