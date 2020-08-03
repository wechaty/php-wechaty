<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/16
 * Time: 5:37 PM
 */
namespace IO\Github\Wechaty\Puppet;

use IO\Github\Wechaty\Puppet\Cache\CacheFactory;
use IO\Github\Wechaty\Puppet\EventEmitter\EventEmitter;
use IO\Github\Wechaty\Puppet\Exceptions\InvalidArgumentException;
use IO\Github\Wechaty\Puppet\FileBox\FileBox;use IO\Github\Wechaty\Puppet\Schemas\ContactPayload;
use IO\Github\Wechaty\Puppet\Schemas\FriendshipPayload;
use IO\Github\Wechaty\Puppet\Schemas\ImageType;
use IO\Github\Wechaty\Puppet\Schemas\MessagePayload;
use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\Schemas\Query\FriendshipSearchCondition;
use IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter;
use IO\Github\Wechaty\Puppet\Schemas\Query\RoomMemberQueryFilter;
use IO\Github\Wechaty\Puppet\Schemas\Query\RoomQueryFilter;
use IO\Github\Wechaty\Puppet\Schemas\RoomInvitationPayload;
use IO\Github\Wechaty\Puppet\Schemas\RoomMemberPayload;
use IO\Github\Wechaty\Puppet\Schemas\RoomPayload;
use IO\Github\Wechaty\Puppet\Schemas\UrlLinkPayload;
use IO\Github\Wechaty\Util\Logger;
use LM\Exception;

abstract class Puppet extends EventEmitter {
    protected static $_STATE = StateEnum::OFF;

    private $_id = null;

    protected $_puppetOptions = null;
    /**
     * @var Cache\Cache|Cache\Yac|null
     */
    protected $_cache = null;

    const CACHE_CONTACT_PAYLOAD_PREFIX = "ccp_";
    const CACHE_FRIENDSHIP_PAYLOAD_PREFIX = "cfp_";
    const CACHE_MESSAGE_PAYLOAD_PREFIX = "cmp_";
    const CACHE_ROOM_PAYLOAD_PREFIX = "crp_";
    const CACHE_ROOM_MEMBER_PAYLOAD_PREFIX = "crmp_";
    const CACHE_ROOM_INVITATION_PAYLOAD_PREFIX = "crip_";

    public function __construct(PuppetOptions $puppetOptions) {
        if(empty($puppetOptions->token)) {
            throw new InvalidArgumentException("token is null");
        }
        $this->_puppetOptions = $puppetOptions;

        $this->_cache = $this->_initCache();
    }

    protected function _initCache() {
        return CacheFactory::getCache();
    }

    abstract public function start();
    abstract public function stop();
    abstract function setPuppetName();
    abstract function logout(): void;

    abstract function friendshipRawPayload($friendshipId);
    protected abstract function _contactRawPayload(String $contractId) : ContactPayload;
    protected abstract function _contactRawPayloadParser(ContactPayload $rawPayload) : ContactPayload;
    abstract function _messageRawPayload(String $messageId) : MessagePayload;
    abstract function _messageRawPayloadParser(MessagePayload $rawPayload) : MessagePayload;
    abstract function _roomRawPayload(String $roomId) : RoomPayload;
    abstract function _roomRawPayloadParser(RoomPayload $roomPayload) : RoomPayload;
    protected abstract function _roomMemberRawPayload(String $roomId, String $contactId): RoomMemberPayload;
    protected abstract function _roomMemberRawPayloadParser(RoomMemberPayload $rawPayload): RoomMemberPayload;

    abstract function messageSendContact(String $conversationId, String $contactId) : String;
    abstract function messageSendFile(String $conversationId, FileBox $file) : String;

    abstract function messageSendMiniProgram(String $conversationId, MiniProgramPayload $miniProgramPayload) : String;
    abstract function messageSendText(String $conversationId, String $text, array $mentionList = array()) : String;
    abstract function messageSendUrl(String $conversationId, UrlLinkPayload $urlLinkPayload) : String;

    abstract function messageRecall(String $messageId) : bool;

    abstract function messageContact(String $messageId) : String;
    abstract function messageFile(String $messageId) : FileBox;
    abstract function messageImage(String $messageId, int $imageType): FileBox;
    abstract function messageMiniProgram(String $messageId): MiniProgramPayload;
    abstract function messageUrl(String $messageId) : UrlLinkPayload;

    abstract function friendshipSearchPhone(String $phone) : ?String;
    abstract function friendshipSearchWeixin(String $weixin) : ?String;
    abstract function friendshipAccept(String $friendshipId) : void;
    abstract function friendshipAdd(String $contractId, String $hello) : void;

    /**
     * Room
     */
    abstract function roomAdd(String $roomId, String $contactId) : void;

    abstract function roomAvatar(String $roomId) : FileBox;
    abstract function roomCreate(array $contactIdList, String $topic) : String;

    abstract function roomDel(String $roomId, String $contactId) : void;

    abstract function roomList(): array;
    abstract function roomQRCode(String $roomId): ?String;
    abstract function roomQuit(String $roomId): void;
    abstract function roomTopic(String $roomId): ?String;
    abstract function setRoomTopic(String $roomId, String $topic): void;
    abstract function roomRawPayload(String $roomId): RoomPayload;
    abstract function roomRawPayloadParser(RoomPayload $roomPayload): RoomPayload;

    /**
     * RoomMember
     */
    abstract function getRoomAnnounce(String $roomId): ?String;
    abstract function setRoomAnnounce(String $roomId, String $text): object;
    abstract function roomMemberList(String $roomId): array;

    /**
     * Room Invitation
     *
     */
    abstract function roomInvitationAccept(String $roomInvitation): object;

    protected abstract function roomInvitationRawPayload(String $roomInvitationId): RoomInvitationPayload;
    protected abstract function roomInvitationRawPayloadParser(RoomInvitationPayload $rawPayload): RoomInvitationPayload;

    /**
     * contactSelf
     */
    abstract function contactSelfName(String $name): object;

    abstract function contactSelfQRCode(): String;
    abstract function contactSelfSignature(String $signature): object;

    /**
     *
     * Contact
     *
     */
    abstract function contactList();
    abstract function contactAlias(String $contactId, String $alias = "") : void;
    abstract function setContactAvatar(String $contactId, FileBox $file) : void;
    abstract function getContactAvatar(String $contactId): FileBox;
    protected abstract function contactRawPayload(String $contractId): ContactPayload;
    protected abstract function contactRawPayloadParser(ContactPayload $rawPayload): ContactPayload;

    /**
     *
     * Tag
     * tagContactAdd - add a tag for a Contact. Create it first if it not exist.
     * tagContactRemove - remove a tag from the Contact
     * tagContactDelete - delete a tag from Wechat
     * tagContactList(id) - get tags from a specific Contact
     * tagContactList() - get tags from all Contacts
     *
     */
    abstract function tagContactAdd(String $tagId, String $contactId): object;

    abstract function tagContactDelete(String $tagId): object;
    abstract function tagContactList(String $contactId = ""): array;
    abstract function tagContactRemove(String $tagId, String $contactId): object;

    function roomMemberSearch(String $roomId, RoomMemberQueryFilter $query): array {
        // TODO
    }

    function roomSearch(RoomQueryFilter $query): array {
        $allRoomList = $this->roomList();
        $roomPayloads = array_map(function($value) {
            $roomPayload = $this->roomPayload($value);
            if($roomPayload) {
                return $roomPayload;
            }
        }, $allRoomList);

        if(!empty($query->id)) {
            $roomPayloads = array_filter($roomPayloads, function($value) use ($query) {
                return $value->id == $query->id;
            });
        }

        if(!empty($query->topic)) {
            $roomPayloads = array_filter($roomPayloads, function($value) use ($query) {
                Logger::DEBUG("t.topic is {} and topic is {}", $value->topic, $query->topic);
                $equals = $value->topic == $query->topic;
                Logger::DEBUG("equals is $equals");
                return $equals;
            });
            Logger::DEBUG("roomPayloads is {}", $roomPayloads);
        }
        $roomIdList = array_map(function($value) {
            return $value->id;
        }, $roomPayloads);
        return $roomIdList;
    }

    function roomValidate(String $roomId): bool {
        return true;
    }

    protected function _roomInvitationPayloadCache(String $roomInvitationId): ?RoomInvitationPayload {
        $roomInvitationPayload = $this->_cache->get(self::CACHE_ROOM_INVITATION_PAYLOAD_PREFIX . $roomInvitationId);

        return $roomInvitationPayload;
    }

    public function roomInvitationPayload(String $roomInvitationId): RoomInvitationPayload {
        $cachePayload = $this->_roomInvitationPayloadCache($roomInvitationId);

        if ($cachePayload != null) {
            return $cachePayload;
        }

        $rawPayload = $this->roomInvitationRawPayload($roomInvitationId);
        $payload = $this->roomInvitationRawPayloadParser($rawPayload);

        $this->_cache->set(self::CACHE_ROOM_INVITATION_PAYLOAD_PREFIX . $roomInvitationId, $payload);
        return $payload;
    }

    function contactPayloadDirty(String $contactId) {
        $this->_cache->delete(self::CACHE_CONTACT_PAYLOAD_PREFIX . $contactId);
        return true;
    }

    function contactPayload(String $contactId) : ContactPayload {
        $contactPayload = $this->_contactPayloadCache($contactId);

        if ($contactPayload != null) {
            return $contactPayload;
        }

        $contactRawPayload = $this->_contactRawPayload($contactId);
        $payload = $this->_contactRawPayloadParser($contactRawPayload);

        $this->_cache->set(self::CACHE_CONTACT_PAYLOAD_PREFIX . $contactId, $payload);
        return $payload;
    }

    protected function _contactPayloadCache(String $contactId) : ?ContactPayload {
        $contactPayload = $this->_cache->get(self::CACHE_CONTACT_PAYLOAD_PREFIX . $contactId);

        Logger::DEBUG(array("method" => "_contactPayloadCache", "contactPayload" => $contactPayload, "contactId" => $contactId));

        return $contactPayload;
    }

    function messagePayload(String $messageId): MessagePayload {
        $messagePayload = $this->_cache->get(self::CACHE_MESSAGE_PAYLOAD_PREFIX . $messageId);

        if($messagePayload != null) {
            return $messagePayload;
        }
        $messageRawPayload = $this->_messageRawPayload($messageId);
        $payload = $this->_messageRawPayloadParser($messageRawPayload);

        $this->_cache->set(self::CACHE_MESSAGE_PAYLOAD_PREFIX . $messageId, $payload);
        return $payload;
    }

    function messageList(): array {
        $keys = $this->_cache->keys(self::CACHE_MESSAGE_PAYLOAD_PREFIX);
        return $keys;
    }

    function messageSearch(MessageQueryFilter $query): array {
        Logger::DEBUG("messageSearch {}", $query);

        $allMessageIdList = $this->messageList();

        $messagePayloadList = array_map(function($value) {
            return $this->messagePayload($value);
        }, $allMessageIdList);

        if (!empty($query->fromId)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return $value->fromId == $query->fromId;
            });
        }

        if (!empty($query->id)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return $value->id == $query->id;
            });
        }

        if (!empty($query->roomId)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return $value->roomId == $query->roomId;
            });
        }

        if (!empty($query->toId)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return $value->toId == $query->toId;
            });
        }

        if (!empty($query->text)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return $value->text == $query->text;
            });
        }

        if (!empty($query->textReg)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return preg_match($query->textReg, $value->text ?: "");
            });
        }

        if (!empty($query->type)) {
            $messagePayloadList = array_filter($messagePayloadList, function($value) use ($query) {
                return $value->type == $query->type;
            });
        }
        $messageIdList = array_map(function($value) {
            return $value->id;
        }, $messagePayloadList);

        return $messageIdList;
    }

    function roomPayloadDirty(String $roomId) : void {
        $this->_cache->delete(self::CACHE_ROOM_PAYLOAD_PREFIX . $roomId);
        return;
    }

    function roomMemberPayloadDirty(String $roomId) : void {
        $contactIdList = $this->roomMemberList($roomId);

        foreach($contactIdList as $value) {
            $this->_cache->delete(self::CACHE_ROOM_MEMBER_PAYLOAD_PREFIX . $roomId . $value);
        }
    }

    function roomPayload(String $roomId) : RoomPayload {
        $roomPayload = $this->_cache->get(self::CACHE_ROOM_PAYLOAD_PREFIX . $roomId);

        if($roomPayload != null) {
            return $roomPayload;
        }
        $roomRawPayload = $this->_roomRawPayload($roomId);
        $payload = $this->_roomRawPayloadParser($roomRawPayload);

        $this->_cache->set(self::CACHE_ROOM_PAYLOAD_PREFIX . $roomId, $payload);
        return $payload;
    }

    function friendshipSearch(FriendshipSearchCondition $condition): ?String {
        Logger::DEBUG("friendshipSearch{}", $condition);

        if (!empty($condition->phone)) {
            return $this->friendshipSearchPhone($condition->phone);
        } elseif(!empty($condition->weixin)) {
            return $this->friendshipSearchWeixin($condition->weixin);
        } else {
            throw new InvalidArgumentException("friendshipSearch condition error");
        }
    }

    function friendshipPayload(String $friendshipId, FriendshipPayload $newPayload): void {
        if ($newPayload != null) {
            $this->_cache->set(self::CACHE_FRIENDSHIP_PAYLOAD_PREFIX . $friendshipId, $newPayload);
        }
    }

    function roomMemberPayload(String $roomId, String $memberId): RoomMemberPayload {
        $key = $this->_cacheKeyRoomMember($roomId, $memberId);
        $roomMemberPayload = $this->_cache->get($key);
        if($roomMemberPayload != null) {
            return $roomMemberPayload;
        }
        $rawPayload = $this->_roomMemberRawPayload($roomId, $memberId);
        $payload = $this->_roomMemberRawPayloadParser($rawPayload);

        $this->_cache->set($key, $payload);

        return $payload;
    }

    /**
     * Concat roomId & contactId to one string
     */
    private function _cacheKeyRoomMember(String $roomId, String $contactId) : String {
        return self::CACHE_ROOM_MEMBER_PAYLOAD_PREFIX . "$contactId@@@$roomId";
    }

    function selfId() : String {
        return $this->_id;
    }

    public function logonoff() : bool {
        return $this->_id != null;
    }

    protected function _getId() : string {
        return $this->_id;
    }

    function setId($id) {
        $this->_id = $id;
    }
}