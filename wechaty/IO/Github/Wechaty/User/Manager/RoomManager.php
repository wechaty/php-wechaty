<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 2:33 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\User\Contact;
use IO\Github\Wechaty\User\Room;

class RoomManager extends Accessory {
    const CACHE_ROOM_PREFIX = "cr_";

    public function __construct($wechaty) {
        parent::__construct($wechaty);

        $this->_cache = $this->_initCache();
    }

    function load(String $id) : Room {
        $room = $this->_cache->get(self::CACHE_ROOM_PREFIX . $id);
        if(empty($room)) {
            $room = new Room($this->wechaty, $id);
        }
        $this->_cache->set(self::CACHE_ROOM_PREFIX . $id, $room);
        return $room;
    }
}