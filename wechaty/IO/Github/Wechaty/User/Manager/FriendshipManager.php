<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/26
 * Time: 7:10 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\User\Friendship;

class FriendshipManager extends Accessory {
    function load(String $id): Friendship {
        return new Friendship($this->wechaty, $id);
    }
}