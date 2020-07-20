<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 9:02 AM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Event;

class Event {
    public function __construct() {
    }
}

class ScanStatus {
    const UNKNOWN = -1;
    const CANCEL = 0;
    const WAITING = 1;
    const SCANNED = 2;
    const CONFIRMED = 3;
    const TIMEOUT = 4;

    public static $_STATUS = array(
        self::UNKNOWN,
        self::CANCEL,
        self::WAITING,
        self::SCANNED,
        self::CONFIRMED,
        self::TIMEOUT,
    );

    function getByCode(int $code) : int {
        if(in_array($code, self::$_STATUS)) {
            return $code;
        }
        return self::UNKNOWN;
    }
}

class EventLoginPayload {
    public $contactId = null;

    public function __toString() {
        return "EventLoginPayload(contactId='$this->contactId')";
    }
}

class EventLogoutPayload {
    public $contactId = null;
    public $data = null;
    public function __toString() {
        return "EventLogoutPayload(contactId='$this->contactId', data='$this->data')";
    }
}


class EventMessagePayload {
    public $messageId = null;
    public function __toString() {
        return "EventMessagePayload(messageId='$this->messageId')";
    }
}

class EventRoomInvitePayload {
    public $roomInvitationId = null;
    public function __toString() {
        return "EventRoomInvitePayload(roomInvitationId='$this->roomInvitationId')";
    }
}

class EventRoomJoinPayload {
    public $inviteeIdList = null;
    public $inviterId = null;
    public $roomId = null;
    public $timestamp = null;
    public function __toString() {
        return "EventRoomJoinPayload(inviteeIdList=$this->inviteeIdList, inviterId='$this->inviterId', roomId='$this->roomId', timestamp=$this->timestamp)";
    }
}

class EventRoomLeavePayload {
    public $removeeIdList = null;
    public $removerId = null;
    public $roomId = null;
    public $timestamp = null;
    public function __toString() {
        return "EventRoomLeavePayload(removeeIdList=$this->removeeIdList, removerId='$this->removerId', roomId='$this->roomId', timestamp=$this->timestamp)";
    }
}

class EventRoomTopicPayload {
    public $changerId = null;
    public $newTopic = null;
    public $oldTopic = null;
    public $roomId = null;
    public $timestamp = null;
    public function __toString() {
        return "EventRoomTopicePayload(changerId='$this->changerId', newTopic='$this->newTopic', oldTopic='$this->oldTopic', roomId='$this->roomId', timestamp=$this->timestamp)";
    }
}

class EventScanPayload {
    public $status = null;
    public $qrcode = null;
    public $data = null;
    public function __toString() {
        return "EventScanPayload(status=$this->status, qrcode=$this->qrcode, data=$this->data)";
    }
}

class EventFriendshipPayload {
    public $friendshipId = null;
    public function __toString() {
        return "EventFriendshipPayload(friendshipId='$this->friendshipId')";
    }
}

class EventDongPayload {
    public $data = null;
    public function __toString() {
        return "EventDongPayload(data='$this->data')";
    }
}

class EventErrorPayload {
    public $data = null;
    public function __toString() {
        return "EventErrorPayload(data='$this->data')";
    }
}

class EventReadyPayload {
    public $data = null;
    public function __toString() {
        return "EventReadyPayload(data='$this->data')";
    }
}

class EventResetPayload {
    public $data = null;
    public function __toString() {
        return "EventResetPayload(data='$this->data')";
    }
}

class EventHeartbeatPayload {
    public $data = null;
    public function __toString() {
        return "EventHeartbeatPayload(data='$this->data')";
    }
}