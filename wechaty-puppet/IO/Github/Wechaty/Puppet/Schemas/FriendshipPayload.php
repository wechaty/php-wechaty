<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 9:45 AM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class FriendshipPayload {
    const FRIENDSHIPTYPE_UNKNOWN = 0;
    const FRIENDSHIPTYPE_CONFIRM = 1;
    const FRIENDSHIPTYPE_RECEIVE = 2;
    const FRIENDSHIPTYPE_VERIFY = 3;

    const FRIENDSHIPSCENETYPE_UNKNOWN = 0;
    const FRIENDSHIPSCENETYPE_QQ = 1;
    const FRIENDSHIPSCENETYPE_EMAIL = 2;
    const FRIENDSHIPSCENETYPE_WEIXIN = 3;
    const FRIENDSHIPSCENETYPE_QQTBD = 12;
    const FRIENDSHIPSCENETYPE_ROOM = 14;
    const FRIENDSHIPSCENETYPE_PHONE = 15;
    const FRIENDSHIPSCENETYPE_CARD = 17;
    const FRIENDSHIPSCENETYPE_LOCATION = 18;
    const FRIENDSHIPSCENETYPE_BOTTLE = 25;
    const FRIENDSHIPSCENETYPE_SHAKING = 29;
    const FRIENDSHIPSCENETYPE_QRCODE = 30;

    public $id = null;
    public $contactId = null;
    public $hello = null;
    public $timestamp = null;
    public $scene = null;
    public $type = null;
    public $stranger = null;
    public $ticket = null;

    public static $COLUMNS = array(
        "id",
        "contactId",
        "hello",
        "timestamp",
        "scene",
        "type",
        "stranger",
        "ticket",
    );

    static function fromJson(String $json) : FriendshipPayload {
        $data = json_decode($json, true);

        $payload = new FriendshipPayload();
        foreach(self::$COLUMNS as $value) {
            if(isset($data[$value])) {
                $payload->$value = $data[$value];
            } else {
                $payload->$value = null;
            }
        }

        return $payload;
    }
}