<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 8:05 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class EventEnum {
    const START = 0;

    const FRIENDSHIP = 1;

    const LOGIN = 2;

    const LOGOUT = 3;

    const MESSAGE = 4;

    const ROOM_INVITE = 5;

    const INVITE = 6;

    const ROOM_JOIN = 7;

    const JOIN = 8;

    const ROOM_LEAVE = 9;

    const LEAVE = 10;

    const ROOM_TOPIC = 11;

    const TOPIC = 12;

    const SCAN = 13;

    const DONG = 14;

    const ERROR = 15;

    const READY = 16;

    const RESET = 17;

    const HEART_BEAT = 18;

    const ON = 19;
    const OFF = 20;

    const WATCH_DOG = 21;
}