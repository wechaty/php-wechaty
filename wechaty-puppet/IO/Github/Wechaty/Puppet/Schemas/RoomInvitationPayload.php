<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/27
 * Time: 9:05 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class RoomInvitationPayload extends AbstractPayload {
    public $inviterId = null;
    public $topic = null;
    public $avatar = null;
    public $invitation = null;
    public $memberCount = null;
    public $memberIdList = null;
    public $timestamp = null;
    public $receiverId = null;
}