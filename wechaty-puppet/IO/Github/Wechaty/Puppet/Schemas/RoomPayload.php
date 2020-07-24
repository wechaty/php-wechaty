<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 3:22 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class RoomPayload extends AbstractPayload {
    public $topic = null;
    public $avatar = null;
    public $memberIdList = array();
    public $ownerId = null;
    public $adminIdList = array();
}