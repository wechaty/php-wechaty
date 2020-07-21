<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 3:22 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class RoomMemberPayload extends AbstractPayload {
    public $roomAlias  = null;
    public $inviterId  = null;
    public $avatar  = null;
    public $name  = null;
}