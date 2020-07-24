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
use IO\Github\Wechaty\Puppet\Schemas\ImageType;
use IO\Github\Wechaty\Puppet\Schemas\MessagePayload;
use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\Schemas\Query\FriendshipSearchCondition;use IO\Github\Wechaty\Puppet\Schemas\RoomPayload;
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

    abstract function friendshipRawPayload($friendshipId);
    protected abstract function _contactRawPayload(String $contractId) : ContactPayload;
    protected abstract function _contactRawPayloadParser(ContactPayload $rawPayload) : ContactPayload;
    abstract function _messageRawPayload(String $messageId) : MessagePayload;
    abstract function _messageRawPayloadParser(MessagePayload $rawPayload) : MessagePayload;
    abstract function _roomRawPayload(String $roomId) : RoomPayload;
    abstract function _roomRawPayloadParser(RoomPayload $roomPayload) : RoomPayload;
    abstract function roomMemberList(String $roomId) : array;

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

    abstract function contactAlias(String $contactId, String $alias = "") : void;
    abstract function setContactAvatar(String $contactId, FileBox $file) : void;

    abstract function friendshipSearchPhone(String $phone) : ?String;
    abstract function friendshipSearchWeixin(String $weixin) : ?String;

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

        Logger::DEBUG(array("contactPayload" => $contactPayload, "contactId" => $contactId));

        return $contactPayload;
    }

    function messagePayload(String $messageId): MessagePayload {
        $messagePayload = $this->_cache->get(self::CACHE_MESSAGE_PAYLOAD_PREFIX . $messageId);

        if($messagePayload != null) {
            return $messagePayload;
        }
        $messageRawPayload = $this->_messageRawPayload($messageId);
        $payload = $this->_messageRawPayloadParser($messageRawPayload);

        return $payload;
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

        return $payload;
    }

    function friendshipSearch(FriendshipSearchCondition $condition): ?String {
        Logger::DEBUG("friendshipSearch{}", $condition);

        if (!empty($condition->phone)) {
            $this->friendshipSearchPhone($condition->phone);
        } elseif(!empty($condition->weixin)) {
            $this->friendshipSearchWeixin($condition->weixin);
        } else {
            throw new InvalidArgumentException("friendshipSearch condition error");
        }
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