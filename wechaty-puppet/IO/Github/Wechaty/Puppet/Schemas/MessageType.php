<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/24
 * Time: 10:23 AM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class MessageType {
    const UNKNOWN = 0;
    const ATTACHMENT = 1; // Attach(6),
    const AUDIO = 2; // Audio(1), Voice(34)
    const CONTACT = 3; // ShareCard(42)
    const CHATHISTORY = 4; // ChatHistory(19)
    const EMOTICON = 5; // Sticker: Emoticon(15), Emoticon(47)
    const IMAGE = 6; // Img(2), Image(3)
    const TEXT = 7; // Text(1)
    const LOCATION = 8; // Location(48)
    const MINIPROGRAM = 9; // MiniProgram(33)
    const TRANSFER = 10; // Transfers(2000)
    const REDENVELOPE = 11; // RedEnvelopes(2001)
    const RECALLED = 12; // Recalled(10002)
    const URL = 13; // Url(5)
    const VIDEO = 14;
}