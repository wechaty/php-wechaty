<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 7:52 AM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Schemas\ImageType;

class Image extends Accessory {
    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        parent::__construct($wechaty);
    }

    function thumbnail() : ?FileBox {
        //TODO
        return $this->wechaty->getPuppet()->messageImage($this->_id, ImageType::THUMBNAIL);
    }

    function hd() : ?FileBox {
        return $this->wechaty->getPuppet()->messageImage($this->_id, ImageType::HD);
    }

    function artwork() : FileBox {
        return $this->wechaty->getPuppet()->messageImage($this->_id, ImageType::ARTWORK);
    }
}