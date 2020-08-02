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
use IO\Github\Wechaty\Puppet\Schemas\Query\RoomQueryFilter;
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

    function findAll(RoomQueryFilter $query): array {
        Logger::DEBUG("findAll {}", $query);

        try {
            $roomIdList = $this->wechaty->getPuppet()->roomSearch($query);
            $that = $this;
            $roomList = array_map(function($value) use ($that) {
                return $that->load($value);
            }, $roomIdList);
            try {
                foreach($roomList as $value) {
                    $value->ready();
                }
                return $roomList;
            } catch(\Exception $e) {
                Logger::WARNING("findAll() room.ready() rejection {}", $e->getTrace());
            }
        } catch (\Exception $e) {
            Logger::ERR("findAll() rejected: {}", $e->getTrace());
        }
        return array();
    }

    function find(RoomQueryFilter $query): ?Room {
        $roomList = $this->findAll($query);
        if (empty($roomList)) {
            return null;
        }

        if (count($roomList) > 1) {
            Logger::WARNING("find got more then one{} result", count($roomList));
        }

        foreach($roomList as $value) {
            $valid = $this->wechaty->getPuppet()->roomValidate($value->getId());
            if($valid) {
                Logger::DEBUG("find() confirm room{} with id={} is valid result, return it.", $value, $value->getId());
                return $value;
            } else {
                Logger::DEBUG("find() confirm room{} with id={} is INVALID result, try next", $value, $value->getId());
            }
        }

        return null;
    }
}