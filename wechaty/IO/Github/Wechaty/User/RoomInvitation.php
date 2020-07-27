<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 5:38 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Puppet\Schemas\Date;
use IO\Github\Wechaty\Util\Logger;

class RoomInvitation extends Accessory {
    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        parent::__construct($wechaty);
    }

    function accept() {
        $this->wechaty->getPuppet()->roomInvitationAccept($this->_id);

        $inviter = $this->inviter();
        $topic = $this->topic();

        Logger::DEBUG("accept", array("topic" => $topic, "inviter" => $inviter));
        $inviter->ready();
    }

    function roomTopic() : String {
        return $this->topic();
    }

    function memberCount() : int {
        $payload = $this->wechaty->getPuppet()->roomInvitationPayload($this->_id);
        return $payload->memberCount ?: 0;
    }

    function inviter() : Contact {
        $payload = $this->wechaty->getPuppet()->roomInvitationPayload($this->_id);
        return $this->wechaty->contactManager->load($payload->inviterId);
    }

    function topic() : String {
        $payload = $this->wechaty->getPuppet()->roomInvitationPayload($this->_id);
        return $payload->topic ?: "";
    }

    function date(): Date {
        $payload = $this->wechaty->getPuppet()->roomInvitationPayload($this->_id);
        return new Date($payload->timestamp);
    }

    function age(): int {
        $recvDate = $this->date();
        return time() - $recvDate->getTimestamp();
    }
}