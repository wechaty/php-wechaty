<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 10:23 AM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Puppet\Schemas\ContactPayload;
use IO\Github\Wechaty\PuppetHostie\PuppetHostie;
use IO\Github\Wechaty\Util\Logger;

class Contact extends Accessory {
    /**
     * @var null|ContactPayload
     */
    protected ?ContactPayload $_payload = null;
    /**
     * @var null|PuppetHostie
     */
    protected $_puppet = null;

    public function __construct($wechaty, $id = "") {
        $this->_id = $id;
        $this->_puppet = $wechaty->getPuppet();
        parent::__construct($wechaty);
    }

    function sync() {
        return $this->ready(true);
    }

    function getPayload() : ContactPayload {
        return $this->_payload;
    }

    function ready(bool $forceSyn = false) {
        if (!$forceSyn && $this->isReady()) {
            return true;
        }
        try {
            if ($forceSyn) {
                $this->_puppet->contactPayloadDirty($this->_id);
            }
            $this->_payload = $this->_puppet->contactPayload($this->_id);
        } catch (\Exception $e) {
            Logger::ERR("ready() contactPayload {} error ", $this->_id, $e);
            throw $e;
        }
    }

    function isReady() : bool {
        return ($this->_payload != null && !empty($this->_payload->name));
    }

    function avatar() {

    }

}