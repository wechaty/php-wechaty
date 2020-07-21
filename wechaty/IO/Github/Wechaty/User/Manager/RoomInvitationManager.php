<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 5:35 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\User\RoomInvitation;

class RoomInvitationManager extends Accessory {
    function load(String $id) : RoomInvitation {
        return new RoomInvitation($this->wechaty, $id);
    }
}