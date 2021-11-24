<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 5:39 PM
 */
namespace IO\Github\Wechaty\PuppetService;

use IO\Github\Wechaty\Puppet\FileBox\FileBox;
use IO\Github\Wechaty\Puppet\Puppet;
use IO\Github\Wechaty\Puppet\Schemas\ContactPayload;
use IO\Github\Wechaty\Puppet\Schemas\Event\EventScanPayload;
use IO\Github\Wechaty\Puppet\Schemas\EventEnum;
use IO\Github\Wechaty\Puppet\Schemas\FriendshipPayload;
use IO\Github\Wechaty\Puppet\Schemas\ImageType;
use IO\Github\Wechaty\Puppet\Schemas\MessagePayload;
use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;
use IO\Github\Wechaty\Puppet\Schemas\PuppetOptions;
use IO\Github\Wechaty\Puppet\Schemas\RoomInvitationPayload;
use IO\Github\Wechaty\Puppet\Schemas\RoomMemberPayload;
use IO\Github\Wechaty\Puppet\Schemas\RoomPayload;
use IO\Github\Wechaty\Puppet\Schemas\UrlLinkPayload;
use IO\Github\Wechaty\Puppet\StateEnum;
use IO\Github\Wechaty\PuppetService\Auth\WechatyCA;
use IO\Github\Wechaty\PuppetService\Exceptions\PuppetServiceException;
use IO\Github\Wechaty\Util\Console;
use IO\Github\Wechaty\Util\Logger;
use Wechaty\Puppet\ContactPayloadResponse;
use Wechaty\Puppet\EventResponse;
use Wechaty\Puppet\EventType;
use Wechaty\Puppet\MessagePayloadResponse;
use Wechaty\Puppet\MessageSendTextResponse;
use Wechaty\Puppet\RoomInvitationPayloadResponse;
use Wechaty\Puppet\RoomMemberPayloadResponse;
use Wechaty\Puppet\RoomPayloadResponse;

class PuppetService extends Puppet {
    private $_channel = null;
    /**
     * @var null|\Wechaty\PuppetClient
     */
    private $_grpcClient = null;

    const CHATIE_ENDPOINT = "https://api.chatie.io/v0/hosties/";

    public static function get() {

    }

    public function start() {
        if(self::$_STATE == StateEnum::ON) {
            Logger::WARNING("start() is called on a ON puppet. await ready(on) and return.");
            self::$_STATE = StateEnum::ON;
            return true;
        }
        self::$_STATE = StateEnum::PENDING;

        try {
            $this->_startGrpcClient();

            $startRequest = new \Wechaty\Puppet\StartRequest();
            $this->_grpcClient->Start($startRequest);

            $this->_startGrpcStream();
            self::$_STATE = StateEnum::ON;
        } catch (\Exception $e) {
            Logger::ERR("start() rejection:", $e);
            self::$_STATE = StateEnum::OFF;
        }

        return true;
    }

    public function stop() {
        Logger::DEBUG("stop()");
        if (self::$_STATE == StateEnum::OFF) {
            Logger::WARNING("stop() is called on a OFF puppet. await ready(off) and return.");
            return true;
        }

        try {
            if ($this->logonoff()) {
                $this->emit(EventEnum::LOGOUT, $this->_getId(), "logout");

                $this->setId(null);
            }

            if (!empty($this->_grpcClient)) {
                try {
                    $stopRequest = new \Wechaty\Puppet\StopRequest();
                    $this->_grpcClient->Stop($stopRequest);
                } catch (\Exception $e) {
                    Logger::ERR("stop() this._grpcClient.stop() rejection:", $e);
                }
            } else {
                Logger::WARNING("stop() this._grpcClient not exist");
            }
            $this->_stopGrpcClient();

        } catch (\Exception $e) {
            Logger::WARNING("stop() rejection: ", $e);
        }
        self::$_STATE = StateEnum::OFF;
    }

    function setPuppetName() {
        $this->_puppetOptions->name = "\\IO\\Github\\Wechaty\\PuppetService\\PuppetService";
    }

    function logout(): void {
        if ($this->_getId() == null) {
            throw new PuppetServiceException("logout before login?");
        }

        try {
            $request = new \Wechaty\Puppet\LogoutRequest();
            list($response, $status) = $this->_grpcClient->Logout($request)->wait();
        } catch (\Exception $e) {
            Logger::ERR("logout() rejection: %s", $e->getTrace());
        } finally {
            $this->emit(EventEnum::LOGOUT, $this->_getId(), "logout");
        }
    }

    function friendshipRawPayload($friendshipId) {
        $request = new \Wechaty\Puppet\FriendshipPayloadRequest();
        $request->setId($friendshipId);

        list($response, $status) = $this->_grpcClient->FriendshipPayload($request)->wait();
        $payload = new FriendshipPayload();

        $payload->scene = $response->getScene();
        $payload->stranger = $response->getStranger();
        $payload->ticket = $response->getTicket();
        $payload->type = $response->getType();
        $payload->contactId = $response->getContactId();
        $payload->id = $response->getId();

        return $payload;
    }

    function _contactRawPayload(String $contractId) : ContactPayload {
        $request = new \Wechaty\Puppet\ContactPayloadRequest();
        $request->setId($contractId);

        list($response, $status) = $this->_grpcClient->ContactPayload($request)->wait();
        $payload = new ContactPayload();
        $payload->id = $response->getId();
        $payload->address = $response->getAddress();
        $payload->alias = $response->getAlias();
        $payload->avatar = $response->getAvatar();
        $payload->city = $response->getCity();
        $payload->friend = $response->getFriend();
        $payload->gender = $response->getGender();
        $payload->name = $response->getName();
        $payload->province = $response->getProvince();
        $payload->signature = $response->getSignature();
        $payload->star = $response->getStar();
        $payload->type = $response->getType();
        $payload->weixin = $response->getWeixin();

        return $payload;
    }

    function _contactRawPayloadParser(ContactPayload $rawPayload) : ContactPayload {
        return $rawPayload;
    }

    function _messageRawPayload(String $messageId) : MessagePayload {
        $request = new \Wechaty\Puppet\MessagePayloadRequest();
        $request->setId($messageId);

        list($response, $status) = $this->_grpcClient->MessagePayload($request)->wait();
        $payload = new MessagePayload();
        $payload->id = $messageId;
        $payload->filename = $response->getFilename();
        $payload->fromId = $response->getFromId();
        $payload->text = $response->getText();
        $payload->mentionIdList = $response->getMentionIds();
        $payload->roomId = $response->getRoomId();
        $payload->timestamp = $response->getTimestamp();
        $payload->type = $response->getType();
        $payload->toId = $response->getToId();

        return $payload;
    }

    function _messageRawPayloadParser(MessagePayload $rawPayload) : MessagePayload {
        $rawPayload->mentionIdList = $this->_repeatFieldToArray($rawPayload->mentionIdList);
        return $rawPayload;
    }

    function _roomRawPayload(string $roomId): RoomPayload {
        $request = new \Wechaty\Puppet\RoomPayloadRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomPayload($request)->wait();
        $payload = new RoomPayload($response->getId());
        $payload->adminIdList = $response->getAdminIds();
        $payload->avatar = $response->getAvatar();
        $payload->memberIdList = $response->getMemberIds();
        $payload->ownerId = $response->getOwnerId();
        $payload->topic = $response->getTopic();

        return $payload;
    }

    function _roomRawPayloadParser(RoomPayload $roomPayload): RoomPayload {
        $roomPayload->adminIdList = $this->_repeatFieldToArray($roomPayload->adminIdList);
        $roomPayload->memberIdList = $this->_repeatFieldToArray($roomPayload->memberIdList);
        return $roomPayload;
    }

    function roomMemberList(string $roomId) : array {
        $request = new \Wechaty\Puppet\RoomMemberListRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomMemberList($request)->wait();

        $memberIds = $response->getMemberIds();
        Logger::DEBUG(array("method" => "roomMemberList", "memberIds" => $memberIds));
        //Google\Protobuf\Internal\RepeatedField Object
        $memberList = array();
        if(is_object($memberIds)) {
            $count = $memberIds->count();
            for($i = 0 ; $i < $count ; $i++) {
                $memberList[] = $memberIds->offsetGet($i);
            }
            return $memberList;
        }
        return $memberIds;
    }

    protected function _roomMemberRawPayload(string $roomId, string $contactId): RoomMemberPayload {
        $request = new \Wechaty\Puppet\RoomMemberPayloadRequest();
        $request->setId($roomId);
        $request->setMemberId($contactId);

        list($response, $status) = $this->_grpcClient->RoomMemberPayload($request)->wait();
        $payload = new RoomMemberPayload();
        $payload->avatar = $response->getAvatar();
        $payload->id = $response->getId();
        $payload->inviterId = $response->getInviterId();
        $payload->name = $response->getName();
        $payload->roomAlias = $response->getRoomAlias();

        return $payload;
    }

    protected function _roomMemberRawPayloadParser(RoomMemberPayload $rawPayload): RoomMemberPayload {
        return $rawPayload;
    }

    function messageSendContact(string $conversationId, string $contactId): string {
        $request = new \Wechaty\Puppet\MessageSendContactRequest();
        $request->setContactId($contactId);
        $request->setConversationId($conversationId);

        list($response, $status) = $this->_grpcClient->MessageSendContact($request)->wait();

        return $response->getId()->getValue();
    }

    function messageSendFile(string $conversationId, FileBox $file): string {
        $fileJson = $file->toJsonString();

        Logger::DEBUG("json is $fileJson");
        Logger::DEBUG("json size is " . strlen($fileJson));

        $request = new \Wechaty\Puppet\MessageSendFileRequest();
        $request->setConversationId($conversationId);
        $request->setFilebox($fileJson);

        list($response, $status) = $this->_grpcClient->MessageSendFile($request)->wait();

        return $response->getId()->getValue();
    }

    function messageSendMiniProgram(string $conversationId, MiniProgramPayload $miniProgramPayload): string {
        $miniProgramJson = $miniProgramPayload->toJsonString();

        Logger::DEBUG("json is $miniProgramJson");
        Logger::DEBUG("json size is " . strlen($miniProgramJson));

        $request = new \Wechaty\Puppet\MessageSendMiniProgramRequest();
        $request->setConversationId($conversationId);
        $request->setMiniProgram($miniProgramJson);

        list($response, $status) = $this->_grpcClient->MessageSendMiniProgram($request)->wait();

        return $response->getId()->getValue();
    }

    function messageSendText(string $conversationId, string $text, array $mentionList = array()): string {
        $request = new \Wechaty\Puppet\MessageSendTextRequest();
        $request->setConversationId($conversationId);
        $request->setText($text);
        $request->setMentonalIds($mentionList);

        list($response, $status) = $this->_grpcClient->MessageSendText($request)->wait();
        //Google\Protobuf\StringValue Object
        return $response->getId()->getValue();
    }

    function messageSendUrl(string $conversationId, UrlLinkPayload $urlLinkPayload): string {
        $urlLinkJson = $urlLinkPayload->toJsonString();

        Logger::DEBUG("json is $urlLinkJson");
        Logger::DEBUG("json size is " . strlen($urlLinkJson));

        $request = new \Wechaty\Puppet\MessageSendUrlRequest();
        $request->setConversationId($conversationId);
        $request->setUrlLink($urlLinkJson);

        list($response, $status) = $this->_grpcClient->MessageSendUrl($request)->wait();
        //Google\Protobuf\StringValue Object
        return $response->getId()->getValue();
    }

    function messageRecall(string $messageId): bool {
        $request = new \Wechaty\Puppet\MessageRecallRequest();
        $request->setId($messageId);

        list($response, $status) = $this->_grpcClient->MessageRecall($request)->wait();

        return $response->getSuccess();
    }

    function contactList() : array {
        $request = new \Wechaty\Puppet\ContactListRequest();

        list($response, $status) = $this->_grpcClient->ContactList($request)->wait();

        $ids = $response->getIds();
        $count = $ids->count();
        $ret = array();
        for($i = 0 ; $i < $count ; $i++) {
            $ret[] = $ids->offsetGet($i);
        }
        return $ret;
    }

    /**
     * @param string $messageId
     * @return string
     */
    function messageContact(string $messageId): string {
        $request = new \Wechaty\Puppet\MessageContactRequest();
        $request->setId($messageId);
        Logger::DEBUG("messageContact:$messageId");

        list($response, $status) = $this->_grpcClient->MessageContact($request)->wait();

        if($response) {
            return $response->getId()->getValue();
        } else {
            return "";
        }
    }

    function messageFile(string $messageId): FileBox {
        $request = new \Wechaty\Puppet\MessageFileRequest();
        $request->setId($messageId);

        list($response, $status) = $this->_grpcClient->MessageFile($request)->wait();
        $jsonText = $response->getFilebox();

        return FileBox::fromJson($jsonText);
    }

    function messageImage(string $messageId, int $imageType): FileBox {
        $request = new \Wechaty\Puppet\MessageImageRequest();
        $request->setId($messageId);
        $request->setType($imageType);

        list($response, $status) = $this->_grpcClient->MessageImage($request)->wait();
        $jsonText = $response->getFilebox();

        return FileBox::fromJson($jsonText);
    }

    function messageMiniProgram(string $messageId): MiniProgramPayload {
        $request = new \Wechaty\Puppet\MessageMiniProgramRequest();
        $request->setId($messageId);

        list($response, $status) = $this->_grpcClient->MessageMiniProgram($request)->wait();
        $jsonText = $response->getMiniProgram();

        return MiniProgramPayload::fromJson($jsonText);
    }

    function messageUrl(string $messageId): UrlLinkPayload {
        $request = new \Wechaty\Puppet\MessageUrlRequest();
        $request->setId($messageId);

        list($response, $status) = $this->_grpcClient->MessageUrl($request)->wait();

        $jsonText = $response->getUrlLink();

        return UrlLinkPayload::fromJson($jsonText);
    }

    function contactAlias(string $contactId, string $alias = ""): void {
        $request = new \Wechaty\Puppet\ContactAliasRequest();
        $request->setId($contactId);
        if($alias) {
            $value = new \Google\Protobuf\StringValue();
            $value->setValue($alias);
            $request->setAlias($value);
        }

        list($response, $status) = $this->_grpcClient->ContactAlias($request)->wait();
    }

    function setContactAvatar(String $contactId, FileBox $file) : void {
        $request = new \Wechaty\Puppet\ContactAvatarRequest();

        $toJsonString = $file->toJsonString();

        $request->setId($contactId);
        $value = new \Google\Protobuf\StringValue();
        $value->setValue($toJsonString);
        $request->setFilebox($value);

        list($response, $status) = $this->_grpcClient->ContactAvatar($request)->wait();
    }

    function friendshipSearchPhone(string $phone): ?string {
        $request = new \Wechaty\Puppet\FriendshipSearchPhoneRequest();
        $request->setPhone($phone);

        list($response, $status) = $this->_grpcClient->FriendshipSearchPhone($request)->wait();

        if($response) {
            return $response->getContactId()->getValue();
        } else {
            return null;
        }
    }

    function friendshipSearchWeixin(string $weixin): ?string {
        $request = new \Wechaty\Puppet\FriendshipSearchWeixinRequest();
        $request->setWeixin($weixin);

        list($response, $status) = $this->_grpcClient->FriendshipSearchWeixin($request)->wait();

        if($response) {
            return $response->getContactId()->getValue();
        } else {
            return null;
        }
    }

    function friendshipAccept(string $friendshipId): void {
        $request = new \Wechaty\Puppet\FriendshipAcceptRequest();
        $request->setId($friendshipId);

        list($response, $status) = $this->_grpcClient->FriendshipAccept($request)->wait();
    }

    function friendshipAdd(string $contractId, string $hello): void {
        $request = new \Wechaty\Puppet\FriendshipAddRequest();
        $request->setContactId($contractId);
        $request->setHello($hello);

        list($response, $status) = $this->_grpcClient->FriendshipAdd($request)->wait();
    }

    function roomAdd(string $roomId, string $contactId): void {
        $request = new \Wechaty\Puppet\RoomAddRequest();
        $request->setContactId($contactId);
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomAdd($request)->wait();
    }

    function roomAvatar(string $roomId): FileBox {
        $request = new \Wechaty\Puppet\RoomAvatarRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomAvatar($request)->wait();
        $fileBox = $response->getFilebox();
        return FileBox::fromJson($fileBox);
    }

    function roomCreate(array $contactIdList, string $topic): string {
        $request = new \Wechaty\Puppet\RoomCreateRequest();
        $request->setTopic($topic);
        $request->setContactIds($contactIdList);

        list($response, $status) = $this->_grpcClient->RoomCreate($request)->wait();

        return $response->getId()->getValue();
    }

    function roomDel(string $roomId, string $contactId): void {
        $request = new \Wechaty\Puppet\RoomDelRequest();
        $request->setContactId($contactId);
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomDel($request)->wait();

        //status:{"metadata":[],"code":0,"details":"OK"} $status->details
        Logger::DEBUG("roomDel", array("status" => $status));
    }

    function roomList(): array {
        $request = new \Wechaty\Puppet\RoomListRequest();

        list($response, $status) = $this->_grpcClient->RoomList($request)->wait();

        return $this->_repeatFieldToArray($response->getIds());
    }

    function roomQRCode(string $roomId): ?string {
        $request = new \Wechaty\Puppet\RoomQRCodeRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomQRCode($request)->wait();
        if($response) {
            return $response->getQrcode();
        } else {
            return null;
        }
    }

    function roomQuit(string $roomId): void {
        $request = new \Wechaty\Puppet\RoomQuitRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomQuit($request)->wait();
    }

    function roomTopic(string $roomId): ?string {
        $request = new \Wechaty\Puppet\RoomTopicRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomTopic($request)->wait();

        if($response) {
            return $response->getTopic()->getValue();
        } else {
            return null;
        }
    }

    function setRoomTopic(string $roomId, string $topic): void {
        $value = new \Google\Protobuf\StringValue();
        $value->setValue($topic);
        $request = new \Wechaty\Puppet\RoomTopicRequest();
        $request->setId($roomId);
        $request->setTopic($value);

        list($response, $status) = $this->_grpcClient->RoomTopic($request)->wait();

        //status:{"metadata":[],"code":0,"details":"OK"}
        Logger::DEBUG("setRoomTopic", array("status" => $status));
    }

    function roomRawPayload(string $roomId): RoomPayload {
        $request = new \Wechaty\Puppet\RoomPayloadRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomPayload($request)->wait();
        $payload = new RoomPayload($response->getId());
        $payload->adminIdList = $this->_repeatFieldToArray($response->getAdminIds());
        $payload->avatar = $response->getAvatar();
        $payload->memberIdList = $this->_repeatFieldToArray($response->getMemberIds());
        $payload->ownerId = $response->getOwnerId();
        $payload->topic = $response->getTopic();

        return $payload;
    }

    function roomRawPayloadParser(RoomPayload $roomPayload): RoomPayload {
        return $roomPayload;
    }

    function getRoomAnnounce(string $roomId): ?string {
        $request = new \Wechaty\Puppet\RoomAnnounceRequest();
        $request->setId($roomId);

        list($response, $status) = $this->_grpcClient->RoomAnnounce($request)->wait();

        if($response) {
            return $response->getText()->getValue();
        } else {
            return null;
        }
    }

    function setRoomAnnounce(string $roomId, string $text): object {
        $value = new \Google\Protobuf\StringValue();
        $value->setValue($text);

        $request = new \Wechaty\Puppet\RoomAnnounceRequest();
        $request->setId($roomId);
        $request->setText($value);

        list($response, $status) = $this->_grpcClient->RoomAnnounce($request)->wait();

        return $status;
    }

    function roomInvitationAccept(string $roomInvitation): object {
        $request = new \Wechaty\Puppet\RoomInvitationAcceptRequest();
        $request->setId($roomInvitation);

        list($response, $status) = $this->_grpcClient->RoomInvitationAccept($request)->wait();

        return $status;
    }

    protected function roomInvitationRawPayload(string $roomInvitationId): RoomInvitationPayload {
        $request = new \Wechaty\Puppet\RoomInvitationPayloadRequest();
        $request->setId($roomInvitationId);

        list($response, $status) = $this->_grpcClient->RoomInvitationPayload($request)->wait();

        $payload = new RoomInvitationPayload();
        $payload->avatar = $response->getAvatar();
        $payload->id = $response->getId();
        $payload->invitation = $response->getInvitation();
        $payload->inviterId = $response->getInviterId();
        $payload->memberCount = $response->getMemberCount();
        $payload->memberIdList = $this->_repeatFieldToArray($response->getMemberIds());
        $payload->receiverId = $response->getReceiverId();
        $payload->timestamp = $response->getTimestamp();
        $payload->topic = $response->getTopic();

        return $payload;
    }

    protected function roomInvitationRawPayloadParser(RoomInvitationPayload $rawPayload): RoomInvitationPayload {
        return $rawPayload;
    }

    function contactSelfName(string $name): object {
        $request = new \Wechaty\Puppet\ContactSelfNameRequest();
        $request->setName($name);

        list($response, $status) = $this->_grpcClient->ContactSelfName($request)->wait();

        return $status;
    }

    function contactSelfQRCode(): string {
        $request = new \Wechaty\Puppet\ContactSelfQRCodeRequest();

        list($response, $status) = $this->_grpcClient->ContactSelfQRCode($request)->wait();

        return $response->getQrcode();
    }

    function contactSelfSignature(string $signature): object {
        $request = new \Wechaty\Puppet\ContactSelfSignatureRequest();
        $request->setSignature($signature);

        list($response, $status) = $this->_grpcClient->ContactSelfSignature($request)->wait();

        return $status;
    }

    function getContactAvatar(string $contactId): FileBox {
        $request = new \Wechaty\Puppet\ContactAvatarRequest();
        $request->setId($contactId);

        list($response, $status) = $this->_grpcClient->ContactAvatar($request)->wait();
        $filebox = $response->getFilebox();
        return FileBox::fromJson($filebox->getValue());
    }

    protected function contactRawPayload(string $contractId): ContactPayload {
        $request = new \Wechaty\Puppet\ContactPayloadRequest();
        $request->setId($contractId);

        list($response, $status) = $this->_grpcClient->ContactPayload($request)->wait();
        $payload = new ContactPayload($response->getId());
        $payload->address = $response->getAddress();
        $payload->alias = $response->getAlias();
        $payload->avatar = $response->getAvatar();
        $payload->city = $response->getCity();
        $payload->friend = $response->getFriend();
        $payload->gender = $response->getGender();
        $payload->name = $response->getName();
        $payload->province = $response->getProvince();
        $payload->signature = $response->getSignature();
        $payload->star = $response->getStar();
        $payload->type = $response->getType();
        $payload->weixin = $response->getWeixin();

        return $payload;
    }

    protected function contactRawPayloadParser(ContactPayload $rawPayload): ContactPayload {
        return $rawPayload;
    }

    function tagContactAdd(string $tagId, string $contactId): object {
        $request = new \Wechaty\Puppet\TagContactAddRequest();
        $request->setId($tagId);
        $request->setContactId($contactId);

        list($response, $status) = $this->_grpcClient->TagContactAdd($request)->wait();

        return $status;
    }

    function tagContactDelete(string $tagId): object {
        $request = new \Wechaty\Puppet\TagContactDeleteRequest();
        $request->setId($tagId);

        list($response, $status) = $this->_grpcClient->TagContactDelete($request)->wait();

        return $status;
    }

    function tagContactList(string $contactId = ""): array {
        $request = new \Wechaty\Puppet\TagContactListRequest();
        if(!empty($contactId)) {
            $value = new \Google\Protobuf\StringValue();
            $value->setValue($contactId);

            $request->setContactId($value);
        }

        list($response, $status) = $this->_grpcClient->TagContactList($request)->wait();
        $tagContactList = $this->_repeatFieldToArray($response->getIds());

        return $tagContactList;
    }

    function tagContactRemove(string $tagId, string $contactId): object {
        $request = new \Wechaty\Puppet\TagContactRemoveRequest();
        $request->setId($tagId);
        $request->setContactId($contactId);

        list($response, $status) = $this->_grpcClient->TagContactRemove($request)->wait();

        return $status;
    }

    private function _repeatFieldToArray($repeatField) : array {
        $ret = array();
        if($repeatField instanceof \Google\Protobuf\Internal\RepeatedField) {
            $count = $repeatField->count();
            for($i = 0 ; $i < $count ; $i++) {
                $ret[] = $repeatField->offsetGet($i);
            }
            return $ret;
        }
        return $repeatField;
    }

    private function _startGrpcClient() {
        $endPoint = $this->_puppetOptions ? $this->_puppetOptions->endPoint : "";
        $discoverHostieIp = array();
        if(empty($endPoint)) {
            $discoverHostieIp = $this->_discoverHostieIp();
        } else {
            $split = explode(":", $endPoint);
            if (sizeof($split) == 1) {
                $discoverHostieIp[0] = $split[0];
                $discoverHostieIp[1] = "8788";
            } else {
                $discoverHostieIp = $split;
            }
        }

        if (empty($discoverHostieIp[0]) || $discoverHostieIp[0] == "0.0.0.0") {
            Logger::ERR("cannot get ip by token, check token");
            exit;
        }
        $hostname = $discoverHostieIp[0] . ":" . $discoverHostieIp[1];

        // update
        // \Grpc\ChannelCredentials::createSsl();
        $token = array(
            "token_type" => "Wechaty",
            "access_token" => $this->_puppetOptions ? $this->_puppetOptions->token : "",
        );
        $updateMetadata =
            function ($metadata,
                      $authUri = null,
                      $client = null) use ($token) {
                $metadataCopy = $metadata;
                $metadataCopy["authorization"] =
                    [sprintf('%s %s',
                        $token['token_type'],
                        $token['access_token'])];

                return $metadataCopy;
            };
        Logger::DEBUG($updateMetadata);
        // WECHATY_PUPPET_SERVICE_NO_TLS_INSECURE_CLIENT
        // WECHATY_PUPPET_SERVICE_TLS_CA_CERT
        // WECHATY_PUPPET_SERVICE_TLS_SERVER_NAME
        $noTls = getenv("WECHATY_PUPPET_SERVICE_NO_TLS_INSECURE_CLIENT");
        if($noTls) {
            Logger::DEBUG("start client with no tls");
            $this->_grpcClient = new \Wechaty\PuppetClient($hostname, [
                'credentials' => \Grpc\ChannelCredentials::createInsecure(),
                'update_metadata' => $updateMetadata,
            ]);
        } else {
            Logger::DEBUG("start client with tls");
            $this->_grpcClient = new \Wechaty\PuppetClient($hostname, [
                'credentials' => \Grpc\ChannelCredentials::createSsl(WechatyCA::TLS_CA_CERT, WechatyCA::TLS_INSECURE_SERVER_KEY, WechatyCA::TLS_INSECURE_SERVER_CERT),
                'update_metadata' => $updateMetadata,
            ]);
        }

        return $this->_grpcClient;
    }

    private function _stopGrpcClient() {
        Logger::DEBUG("grpc is shutdown");
        return true;
    }

    private function _startGrpcStream() {
        $eventRequest = new \Wechaty\Puppet\EventRequest();
        $call = $this->_grpcClient->Event($eventRequest);
        $ret = $call->responses();//Generator Object
        while($ret->valid()) {
            // Console::logStr($ret->key() . " ");//0 1 2
            $response = $ret->current();
            $this->_onGrpcStreamEvent($response);
            $ret->next();
        }
        echo "service stopped normally\n";
        Console::log($ret);
        Console::log($ret->getReturn());
    }

    private function _discoverHostieIp() : array {
        $url = self::CHATIE_ENDPOINT . $this->_puppetOptions->token;
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $url);

        $ret = array();
        if($response->getStatusCode() == 200) {
            Logger::DEBUG("$url with response " . $response->getBody());
            $ret = json_decode($response->getBody(), true);
            if(json_last_error()) {
                Logger::ERR("_discoverHostieIp json_decode with error " . json_last_error_msg());
                throw new PuppetServiceException("_discoverHostieIp json_decode with error " . json_last_error_msg());
            }
            return array($ret["ip"], $ret["port"]);
        } else {
            Logger::ERR("_discoverHostieIp request error with not 200, code is " . $response->getStatusCode());
        }

        return $ret;
    }

    private function _onGrpcStreamEvent(EventResponse $event) {
        try {
            $type = $event->getType();
            $payload = $event->getPayload();

            Logger::DEBUG("PuppetService $type payload $payload");

            switch ($type) {
                case EventType::EVENT_TYPE_SCAN:
                    $eventScanPayload = new EventScanPayload($payload);
                    Logger::DEBUG("scan event", array("payload" => $eventScanPayload));
                    $this->emit(EventEnum::SCAN, $eventScanPayload);
                    break;
                case EventType::EVENT_TYPE_HEARTBEAT:
                    // array is easy
                    $this->emit(EventEnum::HEART_BEAT, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_DONG:
                    $this->emit(EventEnum::DONG, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_ERROR:
                    $this->emit(EventEnum::ERROR, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_FRIENDSHIP:
                    $this->emit(EventEnum::FRIENDSHIP, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_LOGIN:
                    $payload = json_decode($payload, true);
                    $this->setId($payload["contactId"]);
                    $this->emit(EventEnum::LOGIN, $payload);
                    break;
                case EventType::EVENT_TYPE_LOGOUT:
                    $this->setId("");
                    $this->emit(EventEnum::LOGOUT, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_MESSAGE:
                    $this->emit(EventEnum::MESSAGE, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_READY:
                    $this->emit(EventEnum::READY, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_ROOM_INVITE:
                    $this->emit(EventEnum::ROOM_INVITE, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_ROOM_JOIN:
                    $this->emit(EventEnum::ROOM_JOIN, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_ROOM_LEAVE:
                    $this->emit(EventEnum::ROOM_LEAVE, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_ROOM_TOPIC:
                    $this->emit(EventEnum::ROOM_TOPIC, json_decode($payload, true));
                    break;
                case EventType::EVENT_TYPE_RESET:
                    break;
                case EventType::EVENT_TYPE_UNSPECIFIED:
                    break;
                default:
                    Console::logStr($event->getType() . " ");//2
                    Console::logStr($event->getPayload() . " ");
                    //{"qrcode":"https://login.weixin.qq.com/l/IaysbZa04Q==","status":5}
                    //{"data":"heartbeat@browserbridge ding","timeout":60000}
                    //$client->DingSimple($dingRequest);
                    //3{"data":"dong"}
                    echo "\n";
            }
        } catch (\Exception $e) {
            Logger::ERR("_onGrpcStreamEvent error", $e);
        }
    }
}