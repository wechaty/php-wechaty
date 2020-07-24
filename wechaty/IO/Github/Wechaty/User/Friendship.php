<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:04 AM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Exceptions\WechatyException;
use IO\Github\Wechaty\Puppet\Schemas\FriendshipPayload;
use IO\Github\Wechaty\Puppet\Schemas\Query\FriendshipSearchCondition;
use IO\Github\Wechaty\Util\Logger;

class Friendship extends Accessory {
    /**
     * @var null|FriendshipPayload
     */
    private ?FriendshipPayload $_payload = null;

    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        parent::__construct($wechaty);
    }

    function getPayload() : FriendshipPayload {
        return $this->_payload;
    }

    function load(String $id) : Friendship {
        $this->_id = $id;
        return $this;
    }

    function search(FriendshipSearchCondition $queryFilter) : ?Contact {
        $contactId = $this->wechaty->getPuppet()->friendshipSearch($queryFilter);
        if(empty($contactId)) {
            return null;
        }
        $contact = $this->wechaty->contactManager->load($contactId);
        $contact->ready();
        return $contact;
    }


    function add(Contact $contact, String $hello) {
        Logger::DEBUG("add contact", array("contact" => $contact, "hello" => $hello));
        $this->wechaty->getPuppet()->friendshipAdd($contact->getId(), $hello);
    }

    function isReady(): bool {
        return $this->_payload != null;
    }

    function ready() {
        if($this->isReady()) {
            return true;
        }
        $this->_payload = $this->wechaty->getPuppet()->friendshipRawPayload($this->_id);
        $this->contact()->ready();
    }

    function contact() : Contact {
        if($this->_payload == null) {
            throw new WechatyException("no payload");
        }
        return $this->wechaty->contactManager->load($this->_payload->contactId);
    }

    function accept() {
        if($this->_payload == null) {
            throw new WechatyException("no payload");
        }

        if($this->_payload->type != FriendshipPayload::FRIENDSHIPTYPE_RECEIVE) {
            throw new WechatyException("accept() need type to be FriendshipType.Receive, but it got a {$this->_payload->type}");
        }

        $this->wechaty->getPuppet()->friendshipAccept($this->_id);

        $contact = $this->contact();
        $contact->ready();
        $contact->sync();
    }
}