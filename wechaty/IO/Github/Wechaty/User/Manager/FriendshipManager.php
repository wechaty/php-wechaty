<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/26
 * Time: 7:10 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Exceptions\WechatyException;
use IO\Github\Wechaty\Puppet\Schemas\FriendshipPayload;
use IO\Github\Wechaty\Puppet\Schemas\Query\FriendshipSearchCondition;
use IO\Github\Wechaty\User\Contact;
use IO\Github\Wechaty\User\Friendship;
use IO\Github\Wechaty\Util\Logger;

class FriendshipManager extends Accessory {
    function load(String $id): Friendship {
        return new Friendship($this->wechaty, $id);
    }

    function search(FriendshipSearchCondition $queryFilter): ?Contact {
        Logger::DEBUG("FriendshipManager search", array("query" => $queryFilter));
        $contactId = $this->wechaty->getPuppet()->friendshipSearch($queryFilter);
        if(empty($contactId)) {
            return null;
        }
        $contact = $this->wechaty->contactManager->load($contactId);
        $contact->ready();
        return $contact;
    }

    function add(Contact $contact, String $hello) {
        Logger::DEBUG("FriendshipManager add", array("contact" => $contact, "hello" => $hello));
        $this->wechaty->getPuppet()->friendshipAdd($contact->getId(), $hello);
    }

    function del(Contact $contact) {
        Logger::DEBUG(__CLASS__ . " " . __METHOD__, array("contact" => $contact));
        throw new WechatyException("to be implemented");
    }

    function fromJSON(String $payload) : Friendship {
        $readValue = FriendshipPayload::fromJson($payload);
        return $this->fromPayload($readValue);
    }

    function fromPayload(FriendshipPayload $friendshipPayload) : Friendship {
        $this->wechaty->getPuppet()->friendshipPayload($friendshipPayload->id, $friendshipPayload);
        return $this->load($friendshipPayload->id);
    }
}