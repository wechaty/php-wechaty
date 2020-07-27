<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/24
 * Time: 8:31 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;

class Tag extends Accessory {
    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        parent::__construct($wechaty);
    }

    function add(Contact $to) {
        $this->wechaty->getPuppet()->tagContactAdd($this->_id, $to->getId());
    }

    function remove(Contact $from) {
        $this->wechaty->getPuppet()->tagContactRemove($this->_id, $from->getId());
    }
}