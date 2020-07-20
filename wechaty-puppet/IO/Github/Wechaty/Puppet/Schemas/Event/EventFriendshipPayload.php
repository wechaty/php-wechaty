<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 9:42 AM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Event;

class EventFriendshipPayload {
    public $friendshipId = null;
    public function __toString() {
        return "EventFriendshipPayload(friendshipId='$this->friendshipId')";
    }
}