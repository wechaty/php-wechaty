<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 8:53 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\User\Image;

class ImageManager extends Accessory {
    function create(String $id) : Image {
        return new Image($this->wechaty, $id);
    }
}