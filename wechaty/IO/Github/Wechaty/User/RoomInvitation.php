<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 5:38 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;

class RoomInvitation extends Accessory {
    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        parent::__construct($wechaty);
    }
}