<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/26
 * Time: 7:10 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
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
}