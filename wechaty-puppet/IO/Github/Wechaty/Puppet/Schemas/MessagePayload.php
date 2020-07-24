<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:13 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class MessagePayload {
    /**
     * MessageTypeUnknown     MessageType = 0
    MessageTypeAttachment  MessageType = 1
    MessageTypeAudio       MessageType = 2
    MessageTypeContact     MessageType = 3
    MessageTypeChatHistory MessageType = 4
    MessageTypeEmoticon    MessageType = 5
    MessageTypeImage       MessageType = 6
    MessageTypeText        MessageType = 7
    MessageTypeLocation    MessageType = 8
    MessageTypeMiniProgram MessageType = 9
    MessageTypeGroupNote   MessageType = 10
    MessageTypeTransfer    MessageType = 11
    MessageTypeRedEnvelope MessageType = 12
    MessageTypeRecalled    MessageType = 13
    MessageTypeURL         MessageType = 14
    MessageTypeVideo       MessageType = 15
     */
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
    const MESSAGETYPE_GROUPNOTE = 10; //
    const MESSAGETYPE_TRANSFER = 11; // Transfers(2000)
    const MESSAGETYPE_REDENVELOPE = 12; // RedEnvelopes(2001)
    const MESSAGETYPE_RECALLED = 13; // Recalled(10002)
    const MESSAGETYPE_URL = 14; // Url(5)
    const MESSAGETYPE_VIDEO = 15;

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