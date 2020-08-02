<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 9:51 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Puppet\Schemas\Query\MessageQueryFilter;
use IO\Github\Wechaty\User\Message;
use IO\Github\Wechaty\Util\Logger;

class MessageManager extends Accessory {
    public function __construct($wechaty) {
        parent::__construct($wechaty);
    }

    function load(String $id) : Message {
        return new Message($this->wechaty, $id);
    }

    function create(String $id) : Message {
        return $this->load($id);
    }

    function find(MessageQueryFilter $query): ?Message {
        $messageList = $this->findAll($query);

        if(empty($messageList)) {
            return null;
        }

        if(count($messageList) > 1){
            Logger::WARNING("findAll() got more than one({}) result", count($messageList));
        }

        return $messageList[0];
    }
}