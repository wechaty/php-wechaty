<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 2:33 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Exceptions\WechatyException;
use IO\Github\Wechaty\User\Contact;
use IO\Github\Wechaty\User\Room;
use IO\Github\Wechaty\Util\Logger;

class RoomManager extends Accessory {
    const CACHE_ROOM_PREFIX = "cr_";

    public function __construct($wechaty) {
        parent::__construct($wechaty);

        $this->_cache = $this->_initCache();
    }

    function create(array $contactList, ?String $topic = ""): Room {
        if (count($contactList) < 2) {
            throw new WechatyException("contactList need at least 2 contact to create a new room");
        }

        $contactIdList = array_map(function($value) {
            return $value->getId();
        }, $contactList);

        try {
            $roomId = $this->wechaty->getPuppet()->roomCreate($contactIdList, $topic);
            $room = $this->load($roomId);
            return $room;
        } catch (\Exception $e) {
            Logger::ERR("create() room error", $e->getTrace());
            throw $e;
        }
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