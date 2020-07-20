<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:13 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class MessagePayload {
    const MESSAGETYPE_UNKNOWN = 0;
    const MESSAGETYPE_ATTACHMENT = 1; // Attach(6),
    const MESSAGETYPE_AUDIO = 2; // Audio(1), Voice(34)
    const MESSAGETYPE_CONTACT = 3; // ShareCard(42)
    const MESSAGETYPE_CHATHISTORY = 4; // ChatHistory(19)
    const MESSAGETYPE_EMOTICON = 5; // Sticker: Emoticon(15), Emoticon(47)
    const MESSAGETYPE_IMAGE = 6; // Img(2), Image(3)
    const MESSAGETYPE_TEXT = 7; // Text(1)
    const MESSAGETYPE_LOCATION = 8; // Location(48)
    const MESSAGETYPE_MINIPROGRAM = 9; // MiniProgram(33)
    const MESSAGETYPE_TRANSFER = 10; // Transfers(2000)
    const MESSAGETYPE_REDENVELOPE = 11; // RedEnvelopes(2001)
    const MESSAGETYPE_RECALLED = 12; // Recalled(10002)
    const MESSAGETYPE_URL = 13; // Url(5)
    const MESSAGETYPE_VIDEO = 14;

    public $id = null;
    public $mentionIdList = null;
    public $filename = null;
    public $text = null;
    public $timestamp = null;
    public $type = null;
    public $fromId = null;
    public $roomId = null;
    public $toId = null;

    public function __toString() {
        return "MessagePayload(id='$this->id', mentionIdList=$this->mentionIdList, filename=$this->filename, text=$this->text, timestamp=$this->timestamp, type=$this->type, fromId=$this->fromId, roomId=$this->roomId, toId=$this->toId)";
    }
}